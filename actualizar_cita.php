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
    $id <= 0 ||
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
    "UPDATE citas
     SET paciente = ?,
         propietario = ?,
         fecha = ?,
         hora = ?,
         motivo = ?,
         estado = ?
     WHERE id = ?"
);

if (!$consulta) {
    die("Error al preparar la actualización: " . $conexion->error);
}

$consulta->bind_param(
    "ssssssi",
    $paciente,
    $propietario,
    $fecha,
    $hora,
    $motivo,
    $estado,
    $id
);

if ($consulta->execute()) {
    header("Location: citas.php?mensaje=actualizada");
    exit;
}

die("Error al actualizar la cita: " . $consulta->error);