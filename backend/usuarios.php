<?php
// Utilidades de la tabla "users"
require_once __DIR__ . '/conexion.php';

/**
 * Guarda (o actualiza) el usuario y sus tokens en la base de datos.
 * Google solo envía refresh_token en el primer consentimiento, por eso
 * en el update se respeta el valor ya almacenado (COALESCE).
 *
 * @param string $google_id  Identificador único del usuario en Google.
 * @param string $nombre     Nombre mostrado del usuario.
 * @param string $email      Email del usuario.
 * @param array  $google_access_token  Array de tokens devuelto por Google (.created, .expires_in, .refresh_token...).
 * @return bool  true si se guardó correctamente, false en caso de error.
 */
function guardar_usuario_en_bd($google_id, $nombre, $email, $google_access_token) {
    global $pdo;

    // Sin conexión a la base de datos no se puede persistir
    if (!$pdo) {
        error_log('No se pudo guardar el usuario en la base de datos: conexión no disponible.');
        return false;
    }

    try {
        $access_token_json = json_encode($google_access_token);
        $refresh_token = $google_access_token['refresh_token'] ?? null;
        $token_expires_at = date('Y-m-d H:i:s', $google_access_token['created'] + $google_access_token['expires_in']);

        $sql = "
            INSERT INTO users (google_id, name, email, access_token, refresh_token, token_expires_at)
            VALUES (:google_id, :name, :email, :access_token, :refresh_token, :token_expires_at)
            ON DUPLICATE KEY UPDATE
              name = VALUES(name),
              email = VALUES(email),
              access_token = VALUES(access_token),
              refresh_token = COALESCE(VALUES(refresh_token), refresh_token),
              token_expires_at = VALUES(token_expires_at)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':google_id'        => $google_id,
            ':name'             => $nombre,
            ':email'            => $email,
            ':access_token'     => $access_token_json,
            ':refresh_token'    => $refresh_token,
            ':token_expires_at' => $token_expires_at,
        ]);
        return true;
    } catch (PDOException $e) {
        error_log('Error al guardar el usuario en la base de datos: ' . $e->getMessage());
        return false;
    }
}

// ===========================================================================
// DISPOSITIVOS RECORDADOS ("recuérdame")
//
// Cuando el usuario inicia sesión se emite una cookie con un token opaco
// aleatorio. En la base de datos solo se almacena su hash SHA-256, nunca el
// token en claro, de modo que leer la base de datos no permite suplantar la
// sesión de ningún dispositivo. Esos dispositivos son los que permiten
// reconstruir la sesión de PHP cuando esta se pierde (por ejemplo, tras los
// 24 minutos de session.gc_maxlifetime, o al cerrar la PWA en el móvil).
// ===========================================================================

// Longitud del token opaco en bytes antes de pasar a hexadecimal.
define('BYTES_TOKEN_DISPOSITIVO', 32);

// Máximos dispositivos recordados por usuario. Al superarlo se descartan los
// menos usados, para que la tabla no crezca de forma indefinida.
define('MAX_DISPOSITIVOS_POR_USUARIO', 10);

/**
 * Calcula el hash con el que se guarda un token de dispositivo.
 *
 * @param string $token_dispositivo  Token opaco recibido en la cookie.
 * @return string  Hash SHA-256 en hexadecimal (64 caracteres).
 */
function hashear_token_dispositivo($token_dispositivo) {
    return hash('sha256', $token_dispositivo);
}

/**
 * Comprueba que un token tiene la forma esperada (64 caracteres hexadecimales).
 * Evita consultas inútiles cuando la cookie llega manipulada o corrupta.
 *
 * @param mixed $token_dispositivo  Valor recibido de la cookie.
 * @return bool  true si el formato es válido, false en caso contrario.
 */
function token_dispositivo_valido($token_dispositivo) {
    return is_string($token_dispositivo)
        && preg_match('/^[a-f0-9]{64}$/', $token_dispositivo) === 1;
}

/**
 * Registra un dispositivo como "recordado" para el usuario indicado y
 * devuelve el token opaco que debe ir en la cookie. En la base de datos solo
 * se guarda su hash.
 *
 * @param string $google_id  Identificador único del usuario en Google.
 * @return string|null  El token en claro, o null si no se pudo registrar.
 */
function registrar_dispositivo($google_id) {
    global $pdo;

    if (!$pdo) {
        error_log('No se pudo registrar el dispositivo: conexión no disponible.');
        return null;
    }

    try {
        // Token aleatorio criptográficamente seguro, en hexadecimal (64 caracteres)
        $token = bin2hex(random_bytes(BYTES_TOKEN_DISPOSITIVO));
        $token_hash = hashear_token_dispositivo($token);

        $sql = "INSERT INTO dispositivos (google_id, token_hash) VALUES (:google_id, :token_hash)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':google_id'  => $google_id,
            ':token_hash' => $token_hash,
        ]);

        // Descartar los dispositivos más antiguos si se supera el máximo
        descartar_dispositivos_sobrantes($google_id);

        return $token;
    } catch (PDOException $e) {
        error_log('Error al registrar el dispositivo: ' . $e->getMessage());
        return null;
    }
}

/**
 * Mantiene como mucho MAX_DISPOSITIVOS_POR_USUARIO dispositivos por usuario,
 * eliminando los que llevan más tiempo sin usarse.
 *
 * @param string $google_id  Identificador único del usuario en Google.
 * @return void
 */
function descartar_dispositivos_sobrantes($google_id) {
    global $pdo;

    if (!$pdo) {
        return;
    }

    try {
        // PDO no permite enlazar un LIMIT con un parámetro, así que el número
        // se inyecta ya casteado a entero: primero se cuenta cuántos sobran y
        // después se borran los más antiguos con ese límite.
        $sql_contar = "SELECT COUNT(*) FROM dispositivos WHERE google_id = :google_id";
        $stmt = $pdo->prepare($sql_contar);
        $stmt->execute([':google_id' => $google_id]);
        $sobrantes = (int) $stmt->fetchColumn() - MAX_DISPOSITIVOS_POR_USUARIO;

        if ($sobrantes <= 0) {
            return;
        }

        $sql_borrar = "
            DELETE FROM dispositivos
            WHERE google_id = :google_id
            ORDER BY ultimo_uso ASC
            LIMIT " . (int) $sobrantes . "
        ";
        $stmt = $pdo->prepare($sql_borrar);
        $stmt->execute([':google_id' => $google_id]);
    } catch (PDOException $e) {
        error_log('Error al descartar dispositivos antiguos: ' . $e->getMessage());
    }
}

/**
 * Busca el usuario asociado a un token de dispositivo y actualiza la marca de
 * último uso. Es el mecanismo que permite recuperar la sesión de PHP.
 *
 * @param string $token_dispositivo  Token opaco recibido en la cookie.
 * @return array|null  Fila del usuario (google_id, name, email, access_token,
 *                     refresh_token), o null si el token no es válido.
 */
function buscar_usuario_por_dispositivo($token_dispositivo) {
    global $pdo;

    if (!$pdo || !token_dispositivo_valido($token_dispositivo)) {
        return null;
    }

    $token_hash = hashear_token_dispositivo($token_dispositivo);

    try {
        $sql = "
            SELECT
              d.google_id    AS google_id,
              u.name         AS name,
              u.email        AS email,
              u.access_token AS access_token,
              u.refresh_token AS refresh_token
            FROM dispositivos d
            INNER JOIN users u ON u.google_id = d.google_id
            WHERE d.token_hash = :token_hash
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':token_hash' => $token_hash]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            return null;
        }

        // ultimo_uso solo se actualiza solo si la fila se modifica, y un SELECT
        // no la modifica: hay que forzarlo para que el recorte de dispositivos
        // antiguos sepa cuál es el más reciente.
        $sql_uso = "UPDATE dispositivos SET ultimo_uso = NOW() WHERE token_hash = :token_hash";
        $stmt = $pdo->prepare($sql_uso);
        $stmt->execute([':token_hash' => $token_hash]);

        return $usuario;
    } catch (PDOException $e) {
        error_log('Error al buscar el usuario por dispositivo: ' . $e->getMessage());
        return null;
    }
}

/**
 * Revoca el dispositivo indicado (usado al cerrar sesión).
 *
 * @param string $token_dispositivo  Token opaco recibido en la cookie.
 * @return bool  true si se ejecutó correctamente, false en caso de error.
 */
function revocar_dispositivo($token_dispositivo) {
    global $pdo;

    if (!$pdo || !token_dispositivo_valido($token_dispositivo)) {
        return false;
    }

    try {
        $sql = "DELETE FROM dispositivos WHERE token_hash = :token_hash";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':token_hash' => hashear_token_dispositivo($token_dispositivo)]);
        return true;
    } catch (PDOException $e) {
        error_log('Error al revocar el dispositivo: ' . $e->getMessage());
        return false;
    }
}

/**
 * Revoca todos los dispositivos de un usuario (usado al cambiar de cuenta).
 * No depende de la clave foránea en cascada, que solo cubre el borrado de la
 * fila en users.
 *
 * @param string $google_id  Identificador único del usuario en Google.
 * @return bool  true si se ejecutó correctamente, false en caso de error.
 */
function revocar_dispositivos_de_usuario($google_id) {
    global $pdo;

    if (!$pdo) {
        return false;
    }

    try {
        $sql = "DELETE FROM dispositivos WHERE google_id = :google_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':google_id' => $google_id]);
        return true;
    } catch (PDOException $e) {
        error_log('Error al revocar los dispositivos del usuario: ' . $e->getMessage());
        return false;
    }
}