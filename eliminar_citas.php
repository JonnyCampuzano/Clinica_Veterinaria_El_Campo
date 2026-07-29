<?php
session_start();

require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: citas.php");
    exit;
}

$id = (int) ($_POST["id"] ?? 0);

if ($id <= 0) {
    header("Location: citas.php");
    exit;
}

$consulta = $conexion->prepare(
    "DELETE FROM citas
     WHERE id = ?"
);

if (!$consulta) {
    die("Error al preparar la eliminación: " . $conexion->error);
}

$consulta->bind_param("i", $id);

if ($consulta->execute()) {
    header("Location: citas.php?mensaje=eliminada");
    exit;
}

die("Error al eliminar la cita: " . $consulta->error);