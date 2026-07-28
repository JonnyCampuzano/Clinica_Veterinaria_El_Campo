<?php
session_start();
require_once "config/conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: nuevo_cliente.php");
    exit;
}

$nombre = trim($_POST["nombre"] ?? "");
$cedula = trim($_POST["cedula"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$direccion = trim($_POST["direccion"] ?? "");

if (
    $nombre === "" ||
    $cedula === "" ||
    $telefono === "" ||
    $correo === "" ||
    $direccion === ""
) {
    die("Todos los campos son obligatorios.");
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    die("El correo electrónico no es válido.");
}

$sql = $conexion->prepare(
    "INSERT INTO clientes
    (nombre, cedula, telefono, correo, direccion)
    VALUES (?, ?, ?, ?, ?)"
);

if (!$sql) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$sql->bind_param(
    "sssss",
    $nombre,
    $cedula,
    $telefono,
    $correo,
    $direccion
);

if ($sql->execute()) {
    header("Location: clientes.php?mensaje=registrado");
    exit;
}

die("Error al guardar el cliente: " . $sql->error);