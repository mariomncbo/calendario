<?php
  require_once __DIR__ . '/google-login-config.php';

// Intercambiar el código de autorización por los tokens de acceso
if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $client->setAccessToken($token);

  // Obtener el perfil del usuario y guardar los tokens para poder persistirlos
  $google_oauth = new Google_Service_Oauth2($client);
  $google_account_info = $google_oauth->userinfo->get();
  $email = $google_account_info->email;
  $name = $google_account_info->name;
  $google_access_token = $token;
}
?>