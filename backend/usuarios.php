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