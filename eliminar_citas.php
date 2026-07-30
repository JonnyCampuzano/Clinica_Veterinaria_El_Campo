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
    header('Location: citas.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: citas.php?mensaje=id_invalido');
    exit;
}

$consulta = $conexion->prepare(
    'DELETE FROM citas WHERE id = ?'
);

if (!$consulta) {
    die('Error al preparar la consulta: ' . $conexion->error);
}

$consulta->bind_param('i', $id);

if (!$consulta->execute()) {
    die('Error al eliminar la cita: ' . $consulta->error);
}

$consulta->close();
$conexion->close();

header('Location: citas.php?mensaje=eliminada');
exit;