<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: nueva_citas.php');
    exit;
}

$paciente    = trim($_POST['paciente'] ?? '');
$propietario = trim($_POST['propietario'] ?? '');
$fecha       = trim($_POST['fecha'] ?? '');
$hora        = trim($_POST['hora'] ?? '');
$motivo      = trim($_POST['motivo'] ?? '');
$estado      = trim($_POST['estado'] ?? '');

$campos = [
    $paciente,
    $propietario,
    $fecha,
    $hora,
    $motivo,
    $estado
];

if (in_array('', $campos, true)) {
    die('Error: todos los campos son obligatorios.');
}

$estadosPermitidos = [
    'Pendiente',
    'Confirmada',
    'Cancelada'
];

if (!in_array($estado, $estadosPermitidos, true)) {
    die('Error: estado no válido.');
}

$sql = 'INSERT INTO citas
        (paciente, propietario, fecha, hora, motivo, estado)
        VALUES (?, ?, ?, ?, ?, ?)';

$consulta = $conexion->prepare($sql);

if (!$consulta) {
    die('Error en la consulta: ' . $conexion->error);
}

$consulta->bind_param(
    'ssssss',
    $paciente,
    $propietario,
    $fecha,
    $hora,
    $motivo,
    $estado
);

if (!$consulta->execute()) {
    die('Error al guardar la cita: ' . $consulta->error);
}

$consulta->close();

header('Location: citas.php?mensaje=registrada');
exit;