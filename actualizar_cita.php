<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Usa esta ruta si conexion.php está dentro de database
require_once __DIR__ . '/database/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: citas.php');
    exit;
}

// Recibir datos
$id          = (int) ($_POST['id'] ?? 0);
$paciente    = trim($_POST['paciente'] ?? '');
$propietario = trim($_POST['propietario'] ?? '');
$fecha       = trim($_POST['fecha'] ?? '');
$hora        = trim($_POST['hora'] ?? '');
$motivo      = trim($_POST['motivo'] ?? '');
$estado      = trim($_POST['estado'] ?? '');

// Validar campos
if (
    $id <= 0 ||
    in_array('', [
        $paciente,
        $propietario,
        $fecha,
        $hora,
        $motivo,
        $estado
    ], true)
) {
    die('Error: todos los campos son obligatorios.');
}

// Validar estado
$estadosPermitidos = ['Pendiente', 'Confirmada', 'Cancelada'];

if (!in_array($estado, $estadosPermitidos, true)) {
    die('Error: el estado seleccionado no es válido.');
}

// Actualizar cita
$sql = "UPDATE citas
        SET paciente = ?,
            propietario = ?,
            fecha = ?,
            hora = ?,
            motivo = ?,
            estado = ?
        WHERE id = ?";

$consulta = $conexion->prepare($sql);

if (!$consulta) {
    die('Error al preparar la consulta: ' . $conexion->error);
}

$consulta->bind_param(
    'ssssssi',
    $paciente,
    $propietario,
    $fecha,
    $hora,
    $motivo,
    $estado,
    $id
);

if (!$consulta->execute()) {
    die('Error al actualizar la cita: ' . $consulta->error);
}

$consulta->close();
$conexion->close();

header('Location: citas.php?mensaje=actualizada');
exit;