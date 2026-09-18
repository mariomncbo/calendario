<?php
//Para poder usar los composer (Google API y phpdotenv)
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Inicializar Dotenv apuntando a .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$clientID = $_ENV['GOOGLE_CLIENT_ID'];
$clientSecret = $_ENV['CLIENT_SECRET'];

$redirectUri = "http://localhost:8888/calendario/frontend/dashboard.php"; //URL donde redirecciona si es correcto el login.

 // create Client Request to access Google API
  $client = new Google_Client();
  $client->setClientId($clientID);
  $client->setClientSecret($clientSecret);
  $client->setRedirectUri($redirectUri);
  $client->addScope("email");
  $client->addScope("profile");
  $client->addScope("https://www.googleapis.com/auth/calendar.events.readonly");
  $client->addScope("https://www.googleapis.com/auth/tasks.readonly");


?>