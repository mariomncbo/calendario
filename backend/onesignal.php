<?php
// Utilidades de OneSignal: llamadas a la API REST, lectura de la agenda del
// día y programación de la notificación diaria con send_after.
// La REST API Key solo se lee aquí desde el .env; nunca llega al frontend.

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/google-login-config.php';

/**
 * Devuelve la configuración de OneSignal leída del .env.
 *
 * @return array{app_id: string, api_key: string}
 */
function config_onesignal() {
    $app_id = $_ENV['ONESIGNAL_APP_ID'] ?? '';
    $api_key = $_ENV['ONESIGNAL_REST_API_KEY'] ?? '';
    return ['app_id' => $app_id, 'api_key' => $api_key];
}

/**
 * Hace una llamada a la API REST de OneSignal con la cabecera de autorización.
 *
 * @param string      $metodo  Método HTTP (GET, POST o DELETE).
 * @param string      $ruta    Ruta de la API, p. ej. "/notifications".
 * @param array|null  $cuerpo  Cuerpo JSON (null para GET/DELETE).
 * @return array|null          Respuesta decodificada, o null si falla.
 */
function peticion_onesignal($metodo, $ruta, $cuerpo = null) {
    $config = config_onesignal();
    if (empty($config['app_id']) || empty($config['api_key'])) {
        error_log('OneSignal: faltan ONESIGNAL_APP_ID u ONESIGNAL_REST_API_KEY en .env');
        return null;
    }

    $curl = curl_init('https://api.onesignal.com' . $ruta);
    $cabeceras = [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Key ' . $config['api_key'],
    ];

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $metodo,
        CURLOPT_HTTPHEADER     => $cabeceras,
        CURLOPT_TIMEOUT        => 30,
    ]);

    if ($cuerpo !== null) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($cuerpo));
    }

    $respuesta = curl_exec($curl);
    $codigo = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $error_curl = curl_error($curl);
    curl_close($curl);

    if ($respuesta === false) {
        // Los errores solo se muestran por consola
        error_log('OneSignal (red): ' . $error_curl . ' [' . $codigo . ']');
        return null;
    }

    $datos = json_decode($respuesta, true);

    // Cancelar una notificación ya enviada devuelve 404, se trata como éxito
    if ($metodo === 'DELETE' && $codigo === 404) {
        return ['removed' => true];
    }
    if ($codigo < 200 || $codigo >= 300) {
        error_log('OneSignal (' . $metodo . ' ' . $ruta . '): ' . $codigo . ' ' . $respuesta);
        return null;
    }

    return $datos;
}

/**
 * Cancela una notificación programada que aún no se ha entregado.
 *
 * @param string|null $id_notificacion  ID devuelto por OneSignal al programar.
 * @return bool  true si se canceló o ya no existía.
 */
function cancelar_notificacion_programada($id_notificacion) {
    $id_notificacion = trim((string) $id_notificacion);
    if ($id_notificacion === '') {
        return true;
    }

    $respuesta = peticion_onesignal('DELETE', '/notifications/' . urlencode($id_notificacion));
    return $respuesta !== null;
}

/**
 * Agenda una notificación push para un usuario concreto en un momento futuro.
 *
 * @param string $external_id  Identificador externo del usuario (google_id).
 * @param string $send_after   Momento de entrega en formato RFC 3339.
 * @param string $titulo       Título del aviso.
 * @param string $contenido    Texto del aviso.
 * @return string|null         ID de la notificación programada, o null si falla.
 */
function agendar_notificacion_onesignal($external_id, $send_after, $titulo, $contenido) {
    $config = config_onesignal();
    if (empty($config['app_id'])) {
        return null;
    }

    $cuerpo = [
        'app_id'            => $config['app_id'],
        'target_channel'    => 'push',
        'headings'          => ['en' => $titulo],
        'contents'          => ['en' => $contenido],
        'include_aliases'   => ['external_id' => [$external_id]],
        'send_after'        => $send_after,
    ];

    $respuesta = peticion_onesignal('POST', '/notifications', $cuerpo);

    if (isset($respuesta['errors'])) {
        // Los errores solo se muestran por consola
        error_log('OneSignal (crear notificación): ' . json_encode($respuesta['errors']));
        return null;
    }
    if (empty($respuesta['id'])) {
        error_log('OneSignal (crear notificación): respuesta sin id');
        return null;
    }

    return $respuesta['id'];
}

/**
 * Crea un cliente de Google autenticado con el access_token del usuario y lo
 * refresca si hace falta, persistiendo el nuevo token en la base de datos.
 *
 * @param string $google_id  Identificador del usuario.
 * @return object|null       Cliente de Google listo, o null si no hay acceso válido.
 */
function crear_cliente_google_de_usuario($google_id) {
    global $pdo, $client;

    $stmt = $pdo->prepare('SELECT access_token, refresh_token FROM users WHERE google_id = :google_id');
    $stmt->execute([':google_id' => $google_id]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fila || empty($fila['access_token'])) {
        error_log('OneSignal: no hay access_token guardado para ' . $google_id);
        return null;
    }

    $client->setAccessToken(json_decode($fila['access_token'], true));
    if (!empty($fila['refresh_token'])) {
        $client->setRefreshToken($fila['refresh_token']);
    }

    // Si el access_token caducó se intenta renovar con el refresh_token
    if ($client->isAccessTokenExpired()) {
        $refresco = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        if (isset($refresco['error'])) {
            error_log('OneSignal (refrescar token): ' . json_encode($refresco));
            return null;
        }
        $client->setAccessToken($refresco);

        $stmt = $pdo->prepare('UPDATE users SET access_token = :access_token WHERE google_id = :google_id');
        $stmt->execute([':access_token' => json_encode($refresco), ':google_id' => $google_id]);
    }

    return $client;
}

/**
 * Lee los eventos de Google Calendar y las tareas de Google Tasks de un día
 * concreto para armar el contenido de la notificación.
 *
 * @param string $google_id  Identificador del usuario.
 * @param string $fecha_iso  Fecha del día en formato Y-m-d.
 * @return array|null        Array con 'eventos' y 'tareas', o null si no hay acceso.
 */
function agenda_del_dia($google_id, $fecha_iso) {
    $cliente = crear_cliente_google_de_usuario($google_id);
    if (!$cliente) {
        return null;
    }

    $inicio_dia = new DateTime($fecha_iso . ' 00:00:00');
    $fin_dia = new DateTime($fecha_iso . ' 23:59:59');

    // --- Eventos de Google Calendar ---
    $eventos = [];
    $calendario = new Google_Service_Calendar($cliente);
    $resultado = $calendario->events->listEvents('primary', [
        'timeMin'      => $inicio_dia->format('c'),
        'timeMax'      => $fin_dia->format('c'),
        'singleEvents' => true,
        'orderBy'      => 'startTime',
    ]);

    foreach ($resultado->getItems() as $evento) {
        $inicio_evento = $evento->getStart();
        $fin_evento = $evento->getEnd();
        $todo_el_dia = empty($inicio_evento->getDateTime());

        $zona_evento = $inicio_evento->getTimeZone()
            ? new DateTimeZone($inicio_evento->getTimeZone())
            : new DateTimeZone(date_default_timezone_get());

        $fecha_inicio = new DateTime(
            $todo_el_dia ? $inicio_evento->getDate() : $inicio_evento->getDateTime(),
            $zona_evento
        );

        if ($todo_el_dia) {
            // Evento de día completo: se tiene en cuenta para cada día que abarque
            $fecha_fin = new DateTime($fin_evento->getDate(), $zona_evento);
            $ultimo_dia = (clone $fecha_fin)->modify('-1 day');
            for ($dia = clone $fecha_inicio; $dia->format('Y-m-d') <= $ultimo_dia->format('Y-m-d'); $dia->modify('+1 day')) {
                if ($dia->format('Y-m-d') === $fecha_iso) {
                    $eventos[] = [
                        'titulo' => $evento->getSummary() ?: '(Sin título)',
                        'hora'   => null,
                    ];
                }
            }
        } elseif ($fecha_inicio->format('Y-m-d') === $fecha_iso) {
            // Evento con hora: se asigna al día en que comienza
            $eventos[] = [
                'titulo' => $evento->getSummary() ?: '(Sin título)',
                'hora'   => $fecha_inicio->format('H:i'),
            ];
        }
    }

    // Ordenar los eventos por hora (los de día completo van al final)
    usort($eventos, function ($a, $b) {
        $hora_a = $a['hora'] ?? '99:99';
        $hora_b = $b['hora'] ?? '99:99';
        return strcmp($hora_a, $hora_b);
    });

    // --- Tareas de Google Tasks con vencimiento ese día ---
    $tareas = [];
    $servicio_tareas = new Google_Service_Tasks($cliente);
    $listas_tareas = $servicio_tareas->tasklists->listTasklists()->getItems() ?: [];

    foreach ($listas_tareas as $lista) {
        $items_tareas = $servicio_tareas->tasks->listTasks($lista->getId(), ['maxResults' => 100])->getItems() ?: [];

        foreach ($items_tareas as $tarea) {
            // Las tareas completadas o sin vencimiento no se notifican
            if ($tarea->getStatus() === 'completed' || empty($tarea->getDue())) {
                continue;
            }
            if (substr($tarea->getDue(), 0, 10) === $fecha_iso) {
                $tareas[] = [
                    'titulo' => $tarea->getTitle() ?: '(Tarea sin título)',
                ];
            }
        }
    }

    return ['eventos' => $eventos, 'tareas' => $tareas];
}

/**
 * Compone el texto de la notificación diaria a partir de la agenda del día:
 * un resumen con los recuentos y hasta tres compromisos para dar contexto.
 *
 * @param array $agenda  Resultado de agenda_del_dia().
 * @return string        Texto del aviso.
 */
function texto_notificacion_dia($agenda) {
    $num_eventos = count($agenda['eventos']);
    $num_tareas = count($agenda['tareas']);

    if ($num_eventos === 0 && $num_tareas === 0) {
        return 'Sin eventos ni tareas para hoy.';
    }

    $partes = [];
    if ($num_eventos > 0) {
        $partes[] = $num_eventos . ' evento' . ($num_eventos > 1 ? 's' : '');
    }
    if ($num_tareas > 0) {
        $partes[] = $num_tareas . ' tarea' . ($num_tareas > 1 ? 's' : '');
    }
    $resumen = implode(' y ', $partes) . ' para hoy';

    // Añadir hasta 3 compromisos para dar contexto al aviso
    $detalles = [];
    foreach (array_slice($agenda['eventos'], 0, 3) as $evento) {
        $detalles[] = ($evento['hora'] ? $evento['hora'] . ' ' : '') . $evento['titulo'];
    }
    foreach (array_slice($agenda['tareas'], 0, 3) as $tarea) {
        $detalles[] = 'Tarea: ' . $tarea['titulo'];
    }

    if (count($detalles) > 0) {
        $resumen .= ': ' . implode(' · ', $detalles);
    }

    return $resumen;
}

/**
 * Cancela la notificación programada pendiente del usuario y limpia la columna.
 *
 * @param string $google_id  Identificador del usuario.
 * @return void
 */
function cancelar_pendiente_usuario($google_id) {
    global $pdo;

    $stmt = $pdo->prepare('SELECT notificacion_pendiente_id FROM users WHERE google_id = :google_id');
    $stmt->execute([':google_id' => $google_id]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fila || empty($fila['notificacion_pendiente_id'])) {
        return;
    }

    cancelar_notificacion_programada($fila['notificacion_pendiente_id']);

    $stmt = $pdo->prepare('UPDATE users SET notificacion_pendiente_id = NULL WHERE google_id = :google_id');
    $stmt->execute([':google_id' => $google_id]);
}

/**
 * Programa (o re-programa) la notificación diaria del usuario.
 * Cancela el pendiente anterior para nunca tener dos envíos duplicados.
 *
 * @param string $google_id  Identificador del usuario.
 * @return array             Estado de la programación.
 */
function programar_pendiente_usuario($google_id) {
    global $pdo;

    $stmt = $pdo->prepare(
        'SELECT daily_notifications_active, hora_notificacion, notificacion_pendiente_id
         FROM users WHERE google_id = :google_id'
    );
    $stmt->execute([':google_id' => $google_id]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fila || !$fila['daily_notifications_active'] || empty($fila['hora_notificacion'])) {
        // Notificaciones desactivadas o sin hora: no queda nada pendiente
        cancelar_pendiente_usuario($google_id);
        return ['programada' => false, 'motivo' => 'desactivada'];
    }

    // Próxima ocurrencia de la hora configurada: hoy si aún no ha pasado, si no mañana
    $ahora = new DateTime('now');
    $fecha_entrega = new DateTime($ahora->format('Y-m-d') . ' ' . $fila['hora_notificacion']);
    if ($fecha_entrega <= $ahora) {
        $fecha_entrega->modify('+1 day');
    }
    $fecha_iso = $fecha_entrega->format('Y-m-d');

    // Cancelar la programación anterior para no duplicar envíos
    cancelar_pendiente_usuario($google_id);

    // Contenido fresco en el momento de agendar (método A: se congela hasta la entrega)
    try {
        $agenda = agenda_del_dia($google_id, $fecha_iso);
    } catch (Exception $e) {
        // Los errores solo se muestran por consola
        error_log('programar_pendiente (Google): ' . $e->getMessage());
        return ['programada' => false, 'motivo' => 'error_google'];
    }
    if ($agenda === null) {
        return ['programada' => false, 'motivo' => 'error_google'];
    }

    $id = agendar_notificacion_onesignal(
        $google_id,
        $fecha_entrega->format(DateTime::RFC3339),
        'Hoy',
        texto_notificacion_dia($agenda)
    );
    if (!$id) {
        return ['programada' => false, 'motivo' => 'error_onesignal'];
    }

    // Guardar el ID pendiente para poder cancelarlo en el próximo re-agendado
    $stmt = $pdo->prepare('UPDATE users SET notificacion_pendiente_id = :id WHERE google_id = :google_id');
    $stmt->execute([':id' => $id, ':google_id' => $google_id]);

    return [
        'programada' => true,
        'fecha'      => $fecha_iso,
        'hora'       => $fila['hora_notificacion'],
        'id'         => $id,
    ];
}