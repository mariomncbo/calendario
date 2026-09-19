<?php
session_start();

// Intercambiar el código de Google por tokens y perfil, si venimos del login
require_once __DIR__ . '/../backend/google-login-autentificacion.php';

// Persistir los tokens y los datos del usuario en la sesión
if (isset($google_access_token)) {
  $_SESSION['usuario'] = $name;
  $_SESSION['email'] = $email;
  $_SESSION['google_id'] = $id;
  $_SESSION['google_access_token'] = $google_access_token;

  // Guardar (o actualizar) el usuario y sus tokens en la base de datos
  require_once __DIR__ . '/../backend/usuarios.php';
  guardar_usuario_en_bd($id, $name, $email, $google_access_token);

  // Redirigir a una URL limpia (PRG): el código de Google solo vale una vez
  header("Location: dashboard.php");
  exit();
}

// Si el usuario canceló el consentimiento, avisamos por consola en el login
if (isset($_GET['error'])) {
  header("Location: index.php?error=login_denegado");
  exit();
}

// Control de acceso: sin sesión activa se vuelve al login
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>DASHBOARD</h1>
  <div class="perfil">
    <h2>Bienvenido <?php echo $_SESSION['usuario'] ?></h2>
    <h3><?php echo $_SESSION['email']?></h3>
    <a href="logout.php">Cerrar sesión</a>
  </div>
</body>
</html>