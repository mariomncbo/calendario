<?php
// Endpoint interno: elimina los datos del usuario de la base de datos y
// cierra su sesión. El frontend lo consume con fetch() desde los ajustes.

header('Content-Type: application/json');

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/gestion-sesion.php';

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
    // Guardar el identificador antes de destruir la sesión para poder usarlo
    $google_id = $_SESSION['google_id'];

    // Borrar el usuario y sus tokens de la base de datos. La clave foránea en
    // cascada elimina además sus dispositivos recordados.
    $sql = "DELETE FROM users WHERE google_id = :google_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':google_id' => $google_id]);

    // Cerrar la sesión y revocar los dispositivos recordados
    cerrar_sesion($google_id);

    $respuesta = ['success' => true, 'errores' => []];
} catch (PDOException $e) {
    // Los errores solo se muestran por consola
    error_log('delete-account (BD): ' . $e->getMessage());
    http_response_code(500);
    $respuesta['errores'][] = 'Error interno de base de datos.';
}

echo json_encode($respuesta);
exit();