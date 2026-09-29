<?php
// Reconstruction de la sesión de PHP a partir de la base de datos.
//
// La sesión de PHP es efímera: su cookie es de sesión (muere al cerrar el
// navegador o la PWA) y su fichero se recolecta a los 24 minutos
// (session.gc_maxlifetime), valor que además no se puede cambiar en hosting
// compartido. Como además $_SESSION['usuario'] solo se rellena cuando Google
// devuelve el código de autorización, el usuario quedaba expulsado casi siempre.
//
// La solución es una segunda cookie, de larga duración, con un token opaco
// aleatorio del que solo se guarda el hash SHA-256 en la tabla dispositivos.
// Ese token es lo que permite volver a construir la sesión sin pasar por Google.
//
// En ningún momento sale un token de Google al navegador: la cookie solo
// contiene el identificador del dispositivo.

require_once __DIR__ . '/sesion-cookie.php';
require_once __DIR__ . '/usuarios.php';

/**
 * Devuelve el token de dispositivo presente en la cookie, o null si no hay.
 *
 * @return string|null  Token opaco, o null si la cookie no está presente.
 */
function leer_cookie_dispositivo() {
    $token = $_COOKIE[COOKIE_DISPOSITIVO] ?? null;

    if (!token_dispositivo_valido($token)) {
        return null;
    }

    return $token;
}

/**
 * Emite la cookie con el token del dispositivo para recordar la sesión.
 *
 * La cookie no contiene ningún token de Google, solo un identificador
 * aleatorio del que la base de datos únicamente conoce el hash.
 *
 * SameSite=Lax es necesario (y no Strict) porque Google devuelve al usuario
 * desde accounts.google.com en una navegación GET: con Strict la cookie no
 * viajaría en ese salto y la sesión se perdería justo al iniciar sesión. Lax
 * sigue impidiendo que viaje en peticiones POST entre sitios, que es lo que
 * protege frente a CSRF.
 *
 * @param string $token_dispositivo  Token opaco devuelto por registrar_dispositivo().
 * @return bool  true si la cookie se envió correctamente, false en caso contrario.
 */
function emitir_cookie_dispositivo($token_dispositivo) {
    if (!token_dispositivo_valido($token_dispositivo)) {
        return false;
    }

    return setcookie(COOKIE_DISPOSITIVO, $token_dispositivo, [
        'expires'  => time() + (DIAS_COOKIE_DISPOSITIVO * SEGUNDOS_POR_DIA),
        'path'     => '/',
        'secure'   => conexion_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

/**
 * Elimina la cookie del dispositivo del navegador.
 *
 * @return void
 */
function borrar_cookie_dispositivo() {
    setcookie(COOKIE_DISPOSITIVO, '', [
        'expires'  => time() - SEGUNDOS_POR_DIA,
        'path'     => '/',
        'secure'   => conexion_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    unset($_COOKIE[COOKIE_DISPOSITIVO]);
}

/**
 * Elimina la cookie de sesión de PHP del navegador.
 *
 * Hace falta porque la cookie de sesión se ha estirado a 30 días: sin borrarla,
 * tras cerrar sesión el navegador seguiría enviando un PHPSESSID ya destruido.
 *
 * @return void
 */
function borrar_cookie_sesion() {
    $nombre = session_name();
    if (empty($nombre)) {
        return;
    }

    setcookie($nombre, '', [
        'expires'  => time() - SEGUNDOS_POR_DIA,
        'path'     => '/',
        'secure'   => conexion_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    unset($_COOKIE[$nombre]);
}

/**
 * Cierra la sesión por completo: revoca el dispositivo recordado, borra las
 * cookies del navegador y destruye la sesión de PHP.
 *
 * Sin revocar el dispositivo, la cookie de "recuérdame" seguiría siendo válida
 * y restaurar_sesion_desde_bd() volvería a meter al usuario nada más abrir la
 * app: cerrar sesión no serviría de nada.
 *
 * @param string|null $google_id  Identificador del usuario. Si se indica, se
 *                                revocan además TODOS sus dispositivos, que es
 *                                lo correcto al cambiar de cuenta.
 * @return void
 */
function cerrar_sesion($google_id = null) {
    // Revocar el dispositivo con el que se está navegando
    $token_dispositivo = leer_cookie_dispositivo();
    if ($token_dispositivo !== null) {
        revocar_dispositivo($token_dispositivo);
    }
    borrar_cookie_dispositivo();

    // Al cambiar de cuenta no debe quedar ningún dispositivo del usuario anterior
    if (!empty($google_id)) {
        revocar_dispositivos_de_usuario($google_id);
    }

    // Destruir la sesión de PHP (solo si hay una abierta)
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }
    borrar_cookie_sesion();
}

/**
 * Reconstruye la sesión de PHP a partir del token de dispositivo guardado en
 * la base de datos.
 *
 * Debe llamarse después de session_start() y antes de comprobar
 * $_SESSION['usuario']. No hace ninguna llamada a Google: solo repuebla la
 * sesión con lo que hay en la base de datos. Si el access_token estuviera
 * caducado, quien lo use después (backend/obtener_semana.php) lo renovará con
 * el refresh_token que se deja aquí en la sesión.
 *
 * @return bool  true si hay sesión utilizable tras la llamada, false en caso contrario.
 */
function restaurar_sesion_desde_bd() {
    // Ya hay sesión activa: no hay nada que reconstruir
    if (isset($_SESSION['usuario'], $_SESSION['google_access_token'])) {
        return true;
    }

    $token_dispositivo = leer_cookie_dispositivo();
    if ($token_dispositivo === null) {
        return false;
    }

    $usuario = buscar_usuario_por_dispositivo($token_dispositivo);
    if (!$usuario) {
        // Token desconocido, revocado o de una base de datos reiniciada. La
        // cookie se deja intacta a propósito: borrarla aquí expulsaría también
        // al usuario si el fallo fue un error puntual de conexión.
        return false;
    }

    // access_token se guarda en la base de datos como texto JSON
    $tokens = json_decode($usuario['access_token'], true);
    if (!is_array($tokens)) {
        error_log('El access_token guardado para ' . $usuario['google_id'] . ' no es un JSON válido.');
        $tokens = [];
    }

    // El refresh_token suele venir dentro del JSON, pero si el usuario solo
    // hubiera consented una vez puede faltar ahí: se recupera de su columna.
    if (empty($tokens['refresh_token']) && !empty($usuario['refresh_token'])) {
        $tokens['refresh_token'] = $usuario['refresh_token'];
    }

    // Sin access_token no hay forma de llamar a Google: el usuario debe
    // volver a pasar por el login.
    if (empty($tokens['access_token'])) {
        error_log('El dispositivo guardado no tiene access_token: ' . $usuario['google_id']);
        return false;
    }

    $_SESSION['usuario'] = $usuario['name'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['google_id'] = $usuario['google_id'];
    $_SESSION['google_access_token'] = $tokens;

    return true;
}
