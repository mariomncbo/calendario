<?php
// Endpoint interno: elimina los datos del usuario de la base de datos y
// cierra su sesión. El frontend lo consume con fetch() desde los ajustes.

header('Content-Type: application/json');

require_once __DIR__ . '/conexion.php';

session_start();

$respuesta = ['success' => false, 'errores' => []];

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
    // Borrar el usuario y sus tokens de la base de datos
    $sql = "DELETE FROM users WHERE google_id = :google_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':google_id' => $_SESSION['google_id']]);

    // Cerrar la sesión activa
    session_unset();
    session_destroy();

    $respuesta = ['success' => true, 'errores' => []];
} catch (PDOException $e) {
    // Los errores solo se muestran por consola
    error_log('delete-account (BD): ' . $e->getMessage());
    http_response_code(500);
    $respuesta['errores'][] = 'Error interno de base de datos.';
}

echo json_encode($respuesta);
exit();