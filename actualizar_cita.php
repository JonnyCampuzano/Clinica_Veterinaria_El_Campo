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
$fecha = trim($_POST['fecha'] ?? '');
$hora = trim($_POST['hora'] ?? '');
$paciente = trim($_POST['paciente'] ?? '');
$propietario = trim($_POST['propietario'] ?? '');
$motivo = trim($_POST['motivo'] ?? '');
$estado = trim($_POST['estado'] ?? '');

$estadosPermitidos = ['Pendiente', 'Confirmada', 'Cancelada'];

if (
    empty($id) ||
    empty($fecha) ||
    empty($hora) ||
    empty($paciente) ||
    empty($propietario) ||
    empty($motivo) ||
    empty($estado)
) {
    header("Location: editar_cita.php?id=" . urlencode($id) . "&error=campos_vacios");
    exit;
}

if (!in_array($estado, $estadosPermitidos)) {
    header("Location: editar_cita.php?id=" . urlencode($id) . "&error=estado_invalido");
    exit;
}

$sql = $conexion->prepare("
    UPDATE citas
    SET fecha = ?, hora = ?, paciente = ?, propietario = ?, motivo = ?, estado = ?
    WHERE id = ?
");

$sql->bind_param(
    "ssssssi",
    $fecha,
    $hora,
    $paciente,
    $propietario,
    $motivo,
    $estado,
    $id
);

if ($sql->execute()) {
    header("Location: citas.php?success=actualizado");
    exit;
} else {
    header("Location: editar_cita.php?id=" . urlencode($id) . "&error=error_actualizar");
    exit;
}
?>