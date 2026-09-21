<?php
// Endpoint interno: guarda las preferencias de notificación diaria del usuario
// (activada y hora) y programa o cancela el envío pendiente en OneSignal.
// El frontend lo consume con fetch() desde los ajustes.

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

// Validar las entradas recibidas en el cuerpo JSON
$entrada = json_decode(file_get_contents('php://input'), true) ?? [];
$activo = $entrada['daily_notifications_active'] ?? null;
$hora = $entrada['hora_notificacion'] ?? null;

if ($activo !== null && !in_array($activo, [0, 1], true) && $activo !== '0' && $activo !== '1') {
    http_response_code(400);
    $respuesta['errores'][] = 'daily_notifications_active debe ser 0 o 1.';
    echo json_encode($respuesta);
    exit();
}

if ($hora !== null && !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hora)) {
    http_response_code(400);
    $respuesta['errores'][] = 'hora_notificacion debe tener formato HH:MM.';
    echo json_encode($respuesta);
    exit();
}

try {
    // Guardar únicamente los campos que lleguen en la petición
    $campos = [];
    $parametros = [];
    if ($activo !== null) {
        $campos[] = 'daily_notifications_active = :activo';
        $parametros[':activo'] = (int) $activo;
    }
    if ($hora !== null) {
        $campos[] = 'hora_notificacion = :hora';
        $parametros[':hora'] = $hora;
    }

    if (count($campos) > 0) {
        $parametros[':google_id'] = $_SESSION['google_id'];
        $sql = 'UPDATE users SET ' . implode(', ', $campos) . ' WHERE google_id = :google_id';
        $pdo->prepare($sql)->execute($parametros);
    }

    // Programar si sigue activa, cancelar el pendiente si se desactivó
    if ($activo === 0 || $activo === '0') {
        cancelar_pendiente_usuario($_SESSION['google_id']);
        $programacion = ['programada' => false, 'motivo' => 'desactivada'];
    } else {
        $programacion = programar_pendiente_usuario($_SESSION['google_id']);
    }

    $respuesta = [
        'success'      => true,
        'errores'      => [],
        'programacion' => $programacion,
    ];
} catch (PDOException $e) {
    // Los errores solo se muestran por consola
    error_log('guardar_preferencias (BD): ' . $e->getMessage());
    http_response_code(500);
    $respuesta['errores'][] = 'Error interno de base de datos.';
}

echo json_encode($respuesta);
exit();