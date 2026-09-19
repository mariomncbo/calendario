<?php
session_start();

// Cerrar la sesión activa: se vacían las variables y se destruye la sesión
session_unset();
session_destroy();

// Volver al login público
header("Location: ../frontend/index.php");
exit();