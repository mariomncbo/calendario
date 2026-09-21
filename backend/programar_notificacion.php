<?php
// Endpoint interno: vuelve a programar la notificación diaria del usuario con
// el contenido actualizado. Se llama al abrir la aplicación para refrescar el
// texto del aviso si hubo cambios de eventos o tareas.

header('Content-Type: application/json');

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/onesignal.php';

session_start();

$respuesta = ['success' => false, 'errores' => []];

// Zona horaria por defecto de la aplicación
date_default_timezone_set('Europe/Madrid');

// Solo se acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $respuesta['errores'][] = 'Método no permitido.';
    echo json_encode($respuesta);
    exit();
}

// Debe existir una sesión activa
if (!isset($_SESSION['usuario']) || !isset($_SESSION['google_id'])) {
    http_response_code(401);
    $respuesta['errores'][] = 'No hay sesión activa.';
    echo json_encode($respuesta);
    exit();
}

try {
    $respuesta = [
        'success'      => true,
        'errores'      => [],
        'programacion' => programar_pendiente_usuario($_SESSION['google_id']),
    ];
} catch (PDOException $e) {
    // Los errores solo se muestran por consola
    error_log('programar_notificacion (BD): ' . $e->getMessage());
    http_response_code(500);
    $respuesta['errores'][] = 'Error interno de base de datos.';
}

echo json_encode($respuesta);
exit();