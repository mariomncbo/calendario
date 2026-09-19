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
            <h1 class="titulo-semana">14 — 20 Octubre 2024</h1>
            <div class="selectores-fecha"></div>
        </div>

        <div class="rejilla-semana">
            <!-- LUNES 14 -->
            <div class="tarjeta-dia">
                <div class="cabecera-dia">
                    <div>
                        <span class="nombre-dia">LUN</span>
                        <span class="numero-dia">14</span>
                    </div>
                    <span class="contador-dia">3 compromisos</span>
                </div>
                <div class="lista-eventos">
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--secundario"></span>
                            <span class="evento-hora">08:30 — 09:30</span>
                        </div>
                        <p class="evento-titulo">Meditación y Lectura Guiada</p>
                        <span class="evento-detalle">Espacio de concentración individual</span>
                    </div>
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--contorno"></span>
                            <span class="evento-hora">11:00 — 12:30</span>
                        </div>
                        <p class="evento-titulo">Revisión de Arquitectura</p>
                        <span class="evento-detalle">Documento de diseño técnico v2</span>
                    </div>
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--contorno"></span>
                            <span class="evento-hora">16:00 — 17:00</span>
                        </div>
                        <p class="evento-titulo">Sincronización de Equipo</p>
                        <span class="evento-detalle">Prioridades del sprint nórdico</span>
                    </div>
                </div>
            </div>

            <!-- MARTES 15 -->
            <div class="tarjeta-dia">
                <div class="cabecera-dia">
                    <div>
                        <span class="nombre-dia">MAR</span>
                        <span class="numero-dia">15</span>
                    </div>
                    <span class="contador-dia">2 compromisos</span>
                </div>
                <div class="lista-eventos">
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--secundario"></span>
                            <span class="evento-hora">10:00 — 11:30</span>
                        </div>
                        <p class="evento-titulo">Enfoque Profundo: Redacción</p>
                        <span class="evento-detalle">Capítulo 4 del manifiesto</span>
                    </div>
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--contorno"></span>
                            <span class="evento-hora">15:00 — 16:00</span>
                        </div>
                        <p class="evento-titulo">Sesión 1:1 con Dirección</p>
                        <span class="evento-detalle">Revisión trimestral de alcance</span>
                    </div>
                </div>
            </div>

            <!-- MIÉRCOLES 16 (HOY) -->
            <div class="tarjeta-dia tarjeta-dia--hoy">
                <div class="cabecera-dia">
                    <div class="grupo-dia">
                        <div>
                            <span class="nombre-dia nombre-dia--hoy">MIÉ</span>
                            <span class="numero-dia numero-dia--hoy">16</span>
                        </div>
                        <span class="insignia-hoy">HOY</span>
                    </div>
                    <span class="contador-dia contador-dia--hoy">4 compromisos</span>
                </div>
                <div class="lista-eventos">
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--secundario"></span>
                            <span class="evento-hora evento-hora--hoy">09:00 — 10:00</span>
                        </div>
                        <p class="evento-titulo">Meditación y Enfoque</p>
                        <span class="evento-detalle">Silencio matutino y respiración</span>
                    </div>
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--secundario"></span>
                            <span class="evento-hora evento-hora--hoy">11:00 — 12:30</span>
                        </div>
                        <p class="evento-titulo">Revisión de Arquitectura</p>
                        <span class="evento-detalle">Consenso sobre topología modular</span>
                    </div>
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--contorno"></span>
                            <span class="evento-hora evento-hora--hoy">14:00 — 15:00</span>
                        </div>
                        <p class="evento-titulo">Sincronización Estratégica</p>
                        <span class="evento-detalle">Alineamiento de roadmap Q4</span>
                    </div>
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--contorno"></span>
                            <span class="evento-hora evento-hora--hoy">16:30 — 17:15</span>
                        </div>
                        <p class="evento-titulo">Demo v1.2</p>
                        <span class="evento-detalle">Presentación interna de interfaz</span>
                    </div>
                </div>
            </div>

            <!-- JUEVES 17 -->
            <div class="tarjeta-dia">
                <div class="cabecera-dia">
                    <div>
                        <span class="nombre-dia">JUE</span>
                        <span class="numero-dia">17</span>
                    </div>
                    <span class="contador-dia">2 compromisos</span>
                </div>
                <div class="lista-eventos">
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--secundario"></span>
                            <span class="evento-hora">09:30 — 11:30</span>
                        </div>
                        <p class="evento-titulo">Taller de Diseño Orgánico</p>
                        <span class="evento-detalle">Exploración de texturas y espaciado</span>
                    </div>
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--contorno"></span>
                            <span class="evento-hora">15:30 — 16:30</span>
                        </div>
                        <p class="evento-titulo">Balance Semanal de Objetivos</p>
                        <span class="evento-detalle">Métricas clave de atención plena</span>
                    </div>
                </div>
            </div>

            <!-- VIERNES 18 -->
            <div class="tarjeta-dia">
                <div class="cabecera-dia">
                    <div>
                        <span class="nombre-dia">VIE</span>
                        <span class="numero-dia">18</span>
                    </div>
                    <span class="contador-dia">2 compromisos</span>
                </div>
                <div class="lista-eventos">
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--contorno"></span>
                            <span class="evento-hora">10:00 — 11:00</span>
                        </div>
                        <p class="evento-titulo">Cierre de Ciclo Semanal</p>
                        <span class="evento-detalle">Retroalimentación constructiva</span>
                    </div>
                    <div class="evento">
                        <div class="evento-hora-lista">
                            <span class="punto-evento punto-evento--secundario"></span>
                            <span class="evento-hora">12:30 — 13:30</span>
                        </div>
                        <p class="evento-titulo">Desconexión y Orden</p>
                        <span class="evento-detalle">Limpieza de bandeja e intenciones</span>
                    </div>
                </div>
            </div>

            <!-- SÁBADO 19 -->
            <div class="tarjeta-dia tarjeta-dia--fin-de-semana">
                <div class="cabecera-dia">
                    <div>
                        <span class="nombre-dia">SÁB</span>
                        <span class="numero-dia numero-dia--fin-de-semana">19</span>
                    </div>
                    <span class="contador-dia">Sin agenda</span>
                </div>
            </div>

            <!-- DOMINGO 20 -->
            <div class="tarjeta-dia tarjeta-dia--fin-de-semana">
                <div class="cabecera-dia">
                    <div>
                        <span class="nombre-dia">DOM</span>
                        <span class="numero-dia numero-dia--fin-de-semana">20</span>
                    </div>
                    <span class="contador-dia">Sin agenda</span>
                </div>
            </div>
        </div>
    </main>

    <footer class="pie">
        <div class="barra-pie"></div>
    </footer>
</body>
</html>