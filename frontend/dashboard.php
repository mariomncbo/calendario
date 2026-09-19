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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="styles/dashboard.css">
</head>
<body>
    <header class="encabezado">
        <div class="barra-encabezado">
            <div class="marca">
                <span class="marca-nombre">CALENDARIO</span>
                <div class="estado-sincronizacion">
                    <span class="punto-estado"></span>
                    <span class="texto-estado">Sincronizado con Google Calendar</span>
                </div>
            </div>
            <nav class="navegacion">
                <a class="enlace-nav enlace-nav--activo" href="#" aria-current="page">Semana Actual</a>
                <a class="enlace-nav" href="#">Siguiente Semana</a>
            </nav>
            <div class="estado-sincronizacion">
                <span class="material-symbols-outlined icono-ajustes">settings</span>
            </div>
        </div>
    </header>

    <main class="contenido">
        <div class="fila-titulo">
            <h1 class="titulo-semana" id="titulo_semana"></h1>
            <div class="selectores-fecha"></div>
        </div>

        <!-- La rejilla semanal se pinta desde frontend/scripts/dashboard.js -->
        <div class="rejilla-semana" id="rejilla_semana"></div>
    </main>

    <footer class="pie">
        <div class="barra-pie"></div>
    </footer>

    <script src="scripts/dashboard.js"></script>
</body>
</html>