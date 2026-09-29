<?php
// Cerrar sesión: revoca el dispositivo recordado y destruye la sesión.
// Se carga antes de session_start() para poder usar los helpers de sesión.
require_once __DIR__ . '/gestion-sesion.php';

session_start();

// cerrar_sesion() se encarga de todo: revocar el dispositivo, borrar las
// cookies (la del dispositivo y la de sesión) y destruir la sesión de PHP.
cerrar_sesion();

// Volver al login público
header("Location: ../index.php");
exit();
