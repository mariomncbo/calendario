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
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="styles/dashboard.css">
    <script>
        // Aplicar el tema guardado antes de que pinte la página para evitar
        // un parpadeo entre el modo claro y el oscuro.
        (function () {
            try {
                var tema = localStorage.getItem('tema') || 'automatico';
                if (tema === 'automatico') {
                    tema = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oscuro' : 'claro';
                }
                if (tema === 'oscuro') {
                    document.documentElement.setAttribute('data-tema', 'oscuro');
                }
            } catch (error) {
                // Si no hay acceso a localStorage no pasa nada; se usa claro
            }
        })();
    </script>
</head>
<body>
    <header class="encabezado">
        <div class="barra-encabezado">
            <div class="marca">
                <span class="marca-nombre">CALENDARIO</span>
                <div class="estado-sincronizacion">
                    <span class="punto-estado"></span>
                    <span class="texto-estado">Sincronizado con Google Calendar y Google Tasks</span>
                </div>
            </div>
            <nav class="navegacion">
                <a class="enlace-nav enlace-nav--activo" href="#" id="enlace_semana_actual" aria-current="page">Semana Actual</a>
                <a class="enlace-nav" href="#" id="enlace_siguiente_semana">Siguiente Semana</a>
            </nav>
            <div class="estado-sincronizacion">
                <button class="boton-ajustes" id="boton_ajustes" aria-label="Abrir ajustes">
                    <span class="material-symbols-outlined icono-ajustes">settings</span>
                </button>
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

    <!-- Ventana de ajustes (contenido maquetado; sin funcionalidad todavía) -->
    <div class="fondo-modal" id="fondo_modal">
        <div class="ventana-modal" id="ventana_modal" role="dialog" aria-modal="true" aria-label="Ajustes">
            <div class="cabecera-modal">
                <h2 class="titulo-modal">Ajustes</h2>
                <button class="boton-cerrar" id="boton_cerrar" aria-label="Cerrar ajustes">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="perfil">
                <span class="perfil-nombre"><?php echo htmlspecialchars($_SESSION['usuario'] ?? ''); ?></span>
                <span class="perfil-correo"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
            </div>

            <div class="seccion-ajustes">
                <span class="etiqueta-seccion">Modo de color</span>
                <div class="selector-modo" role="radiogroup" aria-label="Modo de color">
                    <button class="opcion-modo" data-tema="claro" role="radio">Claro</button>
                    <button class="opcion-modo" data-tema="oscuro" role="radio">Oscuro</button>
                    <button class="opcion-modo" data-tema="automatico" role="radio">Automático</button>
                </div>
            </div>

            <div class="seccion-ajustes">
                <span class="etiqueta-seccion">Cuenta</span>
                <div class="lista-acciones">
                    <a class="accion" href="index.php">
                        <span class="material-symbols-outlined accion-icono">home</span>
                        Volver al inicio
                    </a>
                    <button class="accion">
                        <span class="material-symbols-outlined accion-icono">loop</span>
                        Cambiar de cuenta
                    </button>
                    <a class="accion" href="../backend/logout.php">
                        <span class="material-symbols-outlined accion-icono">logout</span>
                        Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="scripts/dashboard.js"></script>
</body>
</html>