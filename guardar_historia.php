<?php

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once "config/conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: nueva_historia.php");
    exit;
}

$mascota = trim($_POST["mascota"] ?? "");
$fecha = trim($_POST["fecha"] ?? "");
$diagnostico = trim($_POST["diagnostico"] ?? "");
$tratamiento = trim($_POST["tratamiento"] ?? "");
$veterinario = trim($_POST["veterinario"] ?? "");

if (
    $mascota === "" ||
    $fecha === "" ||
    $diagnostico === "" ||
    $tratamiento === "" ||
    $veterinario === ""
) {
    die("Error: todos los campos son obligatorios.");
}

$sql = $conexion->prepare(
    "INSERT INTO historias_clinicas
    (mascota, fecha, diagnostico, tratamiento, veterinario)
    VALUES (?, ?, ?, ?, ?)"
);

if (!$sql) {
    die("Error en la consulta: " . $conexion->error);
}

$sql->bind_param(
    "sssss",
    $mascota,
    $fecha,
    $diagnostico,
    $tratamiento,
    $veterinario
);

if ($sql->execute()) {
    header("Location: historia_clinica.php?mensaje=registrado");
    exit;
}

die("Error al guardar la historia clínica: " . $sql->error);