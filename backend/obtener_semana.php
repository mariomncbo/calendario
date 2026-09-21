<?php
// Endpoint interno: devuelve en JSON los eventos de Google Calendar y las
// tareas de Google Tasks correspondientes a la semana actual (lunes a domingo).
// El frontend lo consume con fetch() y construye la vista semanal.

header('Content-Type: application/json');

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/google-login-config.php';
require_once __DIR__ . '/usuarios.php';

session_start();

$respuesta = ['success' => false, 'errores' => []];

// Zona horaria por defecto de la aplicación
date_default_timezone_set('Europe/Madrid');

// Solo se acepta GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    $respuesta['errores'][] = 'Método no permitido.';
    echo json_encode($respuesta);
    exit();
}

// Desplazamiento en semanas respecto a la actual (0 = esta semana, 1 = siguiente)
$desplazamiento = isset($_GET['desplazamiento']) ? $_GET['desplazamiento'] : 0;
if (!is_numeric($desplazamiento) || (int) $desplazamiento < 0) {
    http_response_code(400);
    $respuesta['errores'][] = 'El parámetro desplazamiento debe ser un número entero no negativo.';
    echo json_encode($respuesta);
    exit();
}
$desplazamiento = (int) $desplazamiento;

// Debe existir una sesión activa con los tokens de Google
if (!isset($_SESSION['usuario']) || !isset($_SESSION['google_access_token'])) {
    http_response_code(401);
    $respuesta['errores'][] = 'No hay sesión activa.';
    echo json_encode($respuesta);
    exit();
}

try {
    // --- Preparar el cliente de Google con el token de la sesión ---
    $client->setAccessToken($_SESSION['google_access_token']);

    // Si el access_token caducó se intenta renovar con el refresh_token
    if ($client->isAccessTokenExpired()) {
        $refresh_token = $client->getRefreshToken();
        if (!$refresh_token) {
            throw new Exception('El token ha expirado y no se dispone de refresh_token.');
        }
        $token_refrescado = $client->fetchAccessTokenWithRefreshToken($refresh_token);
        if (isset($token_refrescado['error'])) {
            throw new Exception('Error al refrescar el token: ' . json_encode($token_refrescado));
        }

        // Persistir el token renovado en la sesión y en la base de datos
        $_SESSION['google_access_token'] = $client->getAccessToken();
        guardar_usuario_en_bd(
            $_SESSION['google_id'],
            $_SESSION['usuario'],
            $_SESSION['email'],
            $client->getAccessToken()
        );
    }

    // --- Calcular la semana solicitada (lunes a domingo) ---
    // Por defecto la actual; con desplazamiento se suma esa cantidad de semanas.
    $ahora = new DateTime('now');
    $desplazamiento_lunes = (int) $ahora->format('N') - 1; // N: 1=lunes ... 7=domingo
    $inicio_semana = (clone $ahora)
        ->modify("-$desplazamiento_lunes days")
        ->modify('+' . (7 * $desplazamiento) . ' days')
        ->setTime(0, 0, 0);
    $fin_semana = (clone $inicio_semana)->modify('+6 days')->setTime(23, 59, 59);

    $semana = [
        'inicio' => $inicio_semana->format('Y-m-d'),
        'fin'    => $fin_semana->format('Y-m-d'),
        'titulo' => formatear_titulo_semana($inicio_semana, $fin_semana),
    ];

    // --- Eventos de Google Calendar ---
    $eventos = [];
    $calendario = new Google_Service_Calendar($client);
    $resultado_calendario = $calendario->events->listEvents('primary', [
        'timeMin'       => $inicio_semana->format('c'),
        'timeMax'       => $fin_semana->format('c'),
        'singleEvents'  => true,
        'orderBy'       => 'startTime',
    ]);

    foreach ($resultado_calendario->getItems() as $evento) {
        $inicio_evento = $evento->getStart();
        $fin_evento = $evento->getEnd();
        $todo_el_dia = empty($inicio_evento->getDateTime());

        // Zona horaria del evento (o la de la aplicación si no la define)
        $zona_evento = $inicio_evento->getTimeZone()
            ? new DateTimeZone($inicio_evento->getTimeZone())
            : new DateTimeZone(date_default_timezone_get());

        $fecha_inicio = new DateTime(
            $todo_el_dia ? $inicio_evento->getDate() : $inicio_evento->getDateTime(),
            $zona_evento
        );
        $fecha_fin = new DateTime(
            $todo_el_dia ? $fin_evento->getDate() : $fin_evento->getDateTime(),
            $fin_evento->getTimeZone()
                ? new DateTimeZone($fin_evento->getTimeZone())
                : $zona_evento
        );

        // Detalle: descripción, o dirección como alternativa
        $detalle = null;
        if ($evento->getDescription()) {
            $detalle = trim(preg_replace('/\r?\n.*/', '', $evento->getDescription()));
        } elseif ($evento->getLocation()) {
            $detalle = trim($evento->getLocation());
        }

        if ($todo_el_dia) {
            // Evento de día completo: se repite en cada día que abarque
            $ultimo_dia = (clone $fecha_fin)->modify('-1 day');
            for ($dia = clone $fecha_inicio; $dia->format('Y-m-d') <= $ultimo_dia->format('Y-m-d'); $dia->modify('+1 day')) {
                $fecha_iso = $dia->format('Y-m-d');
                if (dentro_de_la_semana($fecha_iso, $inicio_semana, $fin_semana)) {
                    $eventos[] = [
                        'dia'          => $fecha_iso,
                        'titulo'       => $evento->getSummary() ?: '(Sin título)',
                        'hora_inicio'  => null,
                        'hora_fin'     => null,
                        'detalle'      => $detalle,
                        'todo_el_dia'  => true,
                    ];
                }
            }
        } else {
            // Evento con hora: se asigna al día en que comienza
            $fecha_iso = $fecha_inicio->format('Y-m-d');
            if (dentro_de_la_semana($fecha_iso, $inicio_semana, $fin_semana)) {
                $eventos[] = [
                    'dia'          => $fecha_iso,
                    'titulo'       => $evento->getSummary() ?: '(Sin título)',
                    'hora_inicio'  => $fecha_inicio->format('H:i'),
                    'hora_fin'     => $fecha_fin->format('H:i'),
                    'detalle'      => $detalle,
                    'todo_el_dia'  => false,
                ];
            }
        }
    }

    // Ordenar los eventos de cada día por hora de inicio
    usort($eventos, function ($a, $b) {
        $hora_a = $a['hora_inicio'] ?? '99:99'; // los todo_el_dia van al final
        $hora_b = $b['hora_inicio'] ?? '99:99';
        return strcmp($hora_a, $hora_b);
    });

    // --- Tareas de Google Tasks con vencimiento esta semana ---
    $tareas = [];
    $servicio_tareas = new Google_Service_Tasks($client);
    $listas_tareas = $servicio_tareas->tasklists->listTasklists()->getItems();

    foreach ($listas_tareas as $lista) {
        $items_tareas = $servicio_tareas->tasks->listTasks($lista->getId(), ['maxResults' => 100])->getItems();

        foreach ($items_tareas as $tarea) {
            // Las tareas completadas no se muestran en la semana
            if ($tarea->getStatus() === 'completed') {
                continue;
            }
            // Sin fecha de vencimiento no se pueden colocar en un día
            if (empty($tarea->getDue())) {
                continue;
            }

            $fecha_iso = substr($tarea->getDue(), 0, 10);
            if (!dentro_de_la_semana($fecha_iso, $inicio_semana, $fin_semana)) {
                continue;
            }

            $tareas[] = [
                'dia'     => $fecha_iso,
                'titulo'  => $tarea->getTitle() ?: '(Tarea sin título)',
                'detalle' => $tarea->getNotes() ? mb_substr(trim($tarea->getNotes()), 0, 90) : null,
            ];
        }
    }

    // Preferencias de notificación diaria del usuario (para inicializar la UI)
    $preferencias = ['daily_notifications_active' => 0, 'hora_notificacion' => null];
    $stmt = $pdo->prepare(
        'SELECT daily_notifications_active, hora_notificacion FROM users WHERE google_id = :google_id'
    );
    $stmt->execute([':google_id' => $_SESSION['google_id']]);
    $fila_preferencias = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($fila_preferencias) {
        $preferencias = [
            'daily_notifications_active' => (int) $fila_preferencias['daily_notifications_active'],
            'hora_notificacion'          => $fila_preferencias['hora_notificacion'],
        ];
    }

    $respuesta = [
        'success' => true,
        'usuario' => $_SESSION['usuario'],
        'semana'  => $semana,
        'eventos' => $eventos,
        'tareas'  => $tareas,
        'preferencias' => $preferencias,
    ];
} catch (PDOException $e) {
    // Los errores solo se muestran por consola
    error_log('obtener_semana (BD): ' . $e->getMessage());
    http_response_code(500);
    $respuesta['errores'][] = 'Error interno de base de datos.';
} catch (Exception $e) {
    error_log('obtener_semana (Google): ' . $e->getMessage());
    http_response_code(500);
    $respuesta['errores'][] = 'No se pudieron obtener los datos de Google.';
}

echo json_encode($respuesta);
exit();

/**
 * Indica si una fecha (Y-m-d) cae dentro del rango de la semana.
 *
 * @param string   $fecha_iso  Fecha en formato Y-m-d.
 * @param DateTime $inicio     Inicio de la semana.
 * @param DateTime $fin        Fin de la semana.
 * @return bool
 */
function dentro_de_la_semana($fecha_iso, $inicio, $fin) {
    return $fecha_iso >= $inicio->format('Y-m-d') && $fecha_iso <= $fin->format('Y-m-d');
}

/**
 * Compone el título de la semana, p. ej. "14 — 20 Septiembre 2026".
 * Si el rango cruza de mes o de año, se muestra "30 Septiembre — 6 Octubre 2026".
 *
 * @param DateTime $inicio Primer día de la semana.
 * @param DateTime $fin    Último día de la semana.
 * @return string
 */
function formatear_titulo_semana($inicio, $fin) {
    $nombres_meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    $dia_inicio = (int) $inicio->format('j');
    $dia_fin = (int) $fin->format('j');
    $mes_inicio = (int) $inicio->format('n');
    $mes_fin = (int) $fin->format('n');
    $anio_inicio = $inicio->format('Y');
    $anio_fin = $fin->format('Y');

    if ($mes_inicio === $mes_fin && $anio_inicio === $anio_fin) {
        return "{$dia_inicio} — {$dia_fin} {$nombres_meses[$mes_inicio]} {$anio_inicio}";
    }

    return "{$dia_inicio} {$nombres_meses[$mes_inicio]} — {$dia_fin} {$nombres_meses[$mes_fin]} {$anio_fin}";
}
?>