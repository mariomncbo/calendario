<?php
// Arranque de la sesión de PHP y utilidades de cookie compartidas.
//
// Vive en su propio archivo, sin dependencias de base de datos, porque
// index.php (la portada pública) también necesita abrir la sesión. Si tirara de
// gestion-sesion.php, cada visita a la portada abriría una conexión a MySQL.

// Nombre de la cookie con el token del dispositivo.
define('COOKIE_DISPOSITIVO', 'calendario_dispositivo');

// Días de validez de la cookie del dispositivo.
define('DIAS_COOKIE_DISPOSITIVO', 30);

// Segundos de un día, para no repetir la conversión.
define('SEGUNDOS_POR_DIA', 86400);

/**
 * Indica si la petición actual se sirve por HTTPS.
 *
 * En InfinityFree (y en general detrás de un proxy inverso) la petición puede
 * llegar al servidor por HTTP aunque el navegador esté usando HTTPS, por eso
 * se comprueba también la cabecera X-Forwarded-Proto.
 *
 * @return bool  true si la conexión es segura, false en caso contrario.
 */
function conexion_https() {
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    }

    if (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
        return true;
    }

    if (($_SERVER['SERVER_PORT'] ?? '') === '443') {
        return true;
    }

    return false;
}

/**
 * Inicia la sesión de PHP con los mismos parámetros de cookie en toda la app.
 *
 * Se centraliza aquí para que todas las páginas que abren sesión (index.php,
 * dashboard.php, ...) emitan la cookie con la misma duración y atributos; si
 * cada una los pusiera por su cuenta, el navegador recibiría cookies distintas
 * según la página por la que entrara el usuario.
 *
 * Debe llamarse antes de cualquier salida HTML.
 *
 * @return void
 */
function iniciar_sesion() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => DIAS_COOKIE_DISPOSITIVO * SEGUNDOS_POR_DIA,
        'path'     => '/',
        'secure'   => conexion_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}
