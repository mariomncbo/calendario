<?php
// Cambiar de cuenta: cierra la sesión activa y obliga a Google a mostrar
// el selector de cuentas con prompt=select_account.

session_start();

require_once __DIR__ . '/google-login-config.php';

// Recombinar consentimiento + selector de cuentas (garantiza refresh_token
// para la cuenta elegida y muestra el selector).
$client->setPrompt('consent select_account');

// Cerrar la sesión del usuario actual
session_unset();
session_destroy();

// Ir a Google para elegir la nueva cuenta
header("Location: " . $client->createAuthUrl());
exit();