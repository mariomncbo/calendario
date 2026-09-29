<?php
// La portada es pública, pero se abre la sesión igualmente para que la visita
// mantenga viva la sesión de PHP: sin abrirla, PHP no reescribe el fichero de
// sesión y el recolector puede darlo por caducado antes de tiempo.
// Se usa sesion-cookie.php (no gestion-sesion.php) para no conectar a la base
// de datos desde la portada.
require_once __DIR__ . '/backend/sesion-cookie.php';
iniciar_sesion();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#202420">
    <title>Document</title>
</head>
<body>
    <h1>Index</h1>
    <?php require __DIR__ . '/backend/google-login-autentificacion.php'?>
    <a href="<?php echo $client->createAuthUrl() ?>">Iniciar sesión con Google</a>
</body>
</html>