<?php
session_start();

// Intercambiar el código de Google por tokens y perfil, si venimos del login
require_once __DIR__ . '/../backend/google-login-autentificacion.php';

// Persistir los tokens y los datos del usuario en la sesión
if (isset($google_access_token)) {
  $_SESSION['usuario'] = $name;
  $_SESSION['email'] = $email;
  $_SESSION['google_id'] = $id;
  $_SESSION['google_access_token'] = $google_access_token;

  // Guardar (o actualizar) el usuario y sus tokens en la base de datos
  require_once __DIR__ . '/../backend/conexion.php';
  if ($pdo) {
    try {
      $access_token_json = json_encode($google_access_token);
      // Google solo envía refresh_token en el primer consentimiento
      $refresh_token = $google_access_token['refresh_token'] ?? null;
      $token_expires_at = date('Y-m-d H:i:s', $google_access_token['created'] + $google_access_token['expires_in']);

      $sql = "
        INSERT INTO users (google_id, name, email, access_token, refresh_token, token_expires_at)
        VALUES (:google_id, :name, :email, :access_token, :refresh_token, :token_expires_at)
        ON DUPLICATE KEY UPDATE
          name = VALUES(name),
          email = VALUES(email),
          access_token = VALUES(access_token),
          refresh_token = COALESCE(VALUES(refresh_token), refresh_token),
          token_expires_at = VALUES(token_expires_at)
      ";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ':google_id'        => $id,
        ':name'             => $name,
        ':email'            => $email,
        ':access_token'     => $access_token_json,
        ':refresh_token'    => $refresh_token,
        ':token_expires_at' => $token_expires_at,
      ]);
    } catch (PDOException $e) {
      error_log('Error al guardar el usuario en la base de datos: ' . $e->getMessage());
    }
  }
}

// Si el usuario canceló el consentimiento, avisamos por consola en el login
if (isset($_GET['error'])) {
  header("Location: index.php?error=login_denegado");
  exit();
}

// Control de acceso: sin sesión activa se vuelve al login
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>DASHBOARD</h1>
  <div class="perfil">
    <h2>Bienvenido <?php echo $_SESSION['usuario'] ?></h2>
    <h3><?php echo $_SESSION['email']?></h3>
  </div>
</body>
</html>