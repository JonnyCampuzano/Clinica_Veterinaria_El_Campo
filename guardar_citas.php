<?php
session_start();

require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: nueva_citas.php");
    exit;
}

$paciente = trim($_POST["paciente"] ?? "");
$propietario = trim($_POST["propietario"] ?? "");
$fecha = trim($_POST["fecha"] ?? "");
$hora = trim($_POST["hora"] ?? "");
$motivo = trim($_POST["motivo"] ?? "");
$estado = trim($_POST["estado"] ?? "");

$estadosPermitidos = [
    "Pendiente",
    "Confirmada",
    "Cancelada"
];

if (
    $paciente === "" ||
    $propietario === "" ||
    $fecha === "" ||
    $hora === "" ||
    $motivo === "" ||
    $estado === ""
) {
    die("Error: todos los campos son obligatorios.");
}

if (!in_array($estado, $estadosPermitidos, true)) {
    die("Error: el estado seleccionado no es válido.");
}

$consulta = $conexion->prepare(
    "INSERT INTO citas
    (paciente, propietario, fecha, hora, motivo, estado)
    VALUES (?, ?, ?, ?, ?, ?)"
);

if (!$consulta) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$consulta->bind_param(
    "ssssss",
    $paciente,
    $propietario,
    $fecha,
    $hora,
    $motivo,
    $estado
);

if ($consulta->execute()) {
    header("Location: citas.php?mensaje=registrada");
    exit;
}

die("Error al guardar la cita: " . $consulta->error);