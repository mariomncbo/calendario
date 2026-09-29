<?php
//Para poder usar los composer (Google API y phpdotenv)
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Inicializar Dotenv apuntando a .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$clientID = $_ENV['GOOGLE_CLIENT_ID'];
$clientSecret = $_ENV['CLIENT_SECRET'];

// ---------------------------------------------------------------------------
// URL de redirección tras el login (redirect_uri de OAuth 2.0)
//
// Google exige que el redirect_uri coincida EXACTAMENTE (esquema, host, puerto
// y ruta) con uno de los "Redirect URIs autorizados" del cliente OAuth. Si no
// coincide, Google responde con un error redirect_uri_mismatch y el login es
// imposible.
//
// El mismo código se usa en local (MAMP) y en producción (InfinityFree), así que
// el URI se elige según el host de la petición. La lista es una "lista blanca":
// un host desconocido (inyección de cabeceras Host) no puede construir un
// redirect_uri arbitrario, se cae al valor local.
//
// Las rutas NO se derivan de la petición porque este archivo también se incluye
// desde cambiar_cuenta.php, que debe redirigir igualmente a dashboard.php.
// ---------------------------------------------------------------------------
$redirect_uris_permitidos = [
    // MAMP: el proyecto está en htdocs/calendario
    'localhost:8888'                  => 'http://localhost:8888/calendario/dashboard.php',
    // InfinityFree: el proyecto está en la raíz del dominio
    'mi-calendario.infinityfree.io'   => 'https://mi-calendario.infinityfree.io/dashboard.php',
];

// Permite añadir orígenes desde .env sin tocar este archivo (opcional).
if (!empty($_ENV['REDIRECT_URIS_PERMITIDOS'])) {
    $extra = json_decode($_ENV['REDIRECT_URIS_PERMITIDOS'], true);
    if (is_array($extra)) {
        $redirect_uris_permitidos = array_merge($redirect_uris_permitidos, $extra);
    }
}

// Host real de la petición; se comparan sin puerto por defecto para no romper
// si el servidor responde con el puerto explícito (p. ej. mi-dominio.io:443).
$host_peticion = strtolower($_SERVER['HTTP_HOST'] ?? '');

// Buscar el origen permitido que coincida con el host de la petición
$redirectUri = null;
foreach ($redirect_uris_permitidos as $host_permitido => $uri) {
    if ($host_peticion === strtolower($host_permitido)
        || $host_peticion === strtolower($host_permitido) . ':443'
        || $host_peticion === strtolower($host_permitido) . ':80') {
        $redirectUri = $uri;
        break;
    }
}

// Origen no reconocido: avisamos por consola y usamos el URI de MAMP
if ($redirectUri === null) {
    error_log('Origen no permitido para el redirect_uri de Google: ' . $host_peticion);
    $redirectUri = $redirect_uris_permitidos['localhost:8888'];
}

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
// Acceso offline: pedir refresh_token para renovar el access_token sin re-login.
// setPrompt('consent') fuerza a que Google entregue el refresh_token en cada
// primer consentimiento (solo se muestra el aviso la primera vez).
$client->setAccessType("offline");
$client->setPrompt("consent");
$client->addScope("email");
$client->addScope("profile");
$client->addScope("https://www.googleapis.com/auth/calendar.events.readonly");
$client->addScope("https://www.googleapis.com/auth/tasks.readonly");


?>