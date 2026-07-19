<?php
session_start();
require_once __DIR__ . "/config/conexion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: citas.php");
    exit;
}

$id = $_POST['id'] ?? '';

if (!empty($id)) {
    $sql = $conexion->prepare("DELETE FROM citas WHERE id = ?");
    $sql->bind_param("i", $id);
    $sql->execute();
}

header("Location: citas.php?success=eliminado");
exit;
?>