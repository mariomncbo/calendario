<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Index</h1>
    <?php require __DIR__ . '/../backend/google-login-autentificacion.php'?>
    <a href="<?php echo $client->createAuthUrl() ?>">Iniciar sesión con Google</a>
</body>
</html>