<?php
// Cambiar de cuenta: cierra la sesión activa y obliga a Google a mostrar
// el selector de cuentas con prompt=select_account.

require_once __DIR__ . '/gestion-sesion.php';
require_once __DIR__ . '/google-login-config.php';

session_start();

// Recombinar consentimiento + selector de cuentas (garantiza refresh_token
// para la cuenta elegida y muestra el selector).
$client->setPrompt('consent select_account');

// Guardar el usuario actual antes de destruir la sesión: al cambiar de cuenta
// hay que revocar TODOS sus dispositivos, no solo el de este navegador.
$google_id = $_SESSION['google_id'] ?? null;

// Cerrar la sesión y revocar los dispositivos del usuario anterior
cerrar_sesion($google_id);

// Ir a Google para elegir la nueva cuenta
header("Location: " . $client->createAuthUrl());
exit();
