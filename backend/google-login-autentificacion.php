<?php
  require_once __DIR__ . '/google-login-config.php';

// Intercambiar el código de autorización por los tokens de acceso
if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

  // Si el código fue reutilizado o expiró, Google devuelve un array de error
  if (isset($token['access_token'])) {
    $client->setAccessToken($token);

    // Obtener el perfil del usuario y guardar los tokens para poder persistirlos
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $id = $google_account_info->id;
    $email = $google_account_info->email;
    $name = $google_account_info->name;
    $google_access_token = $token;
  } else {
    error_log('Error al intercambiar el código por tokens: ' . json_encode($token));
  }
}
?>