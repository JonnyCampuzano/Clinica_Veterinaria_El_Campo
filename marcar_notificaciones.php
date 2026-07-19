<?php
session_start();
require_once __DIR__ . "/config/conexion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

if ($conexion->query("SHOW TABLES LIKE 'notificaciones'")->num_rows > 0) {
    $conexion->query("UPDATE notificaciones SET leido = 1 WHERE leido = 0");
}

header("Location: dashboard.php");
exit;
?>