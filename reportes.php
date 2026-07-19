<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reportes - Sistema Veterinario</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="app">

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="sidebar-logo">
        <div class="logo-box">🐾</div>
        <div>
            <h2>SISTEMA</h2>
            <p>VETERINARIO</p>
        </div>
    </div>

    <nav>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="citas.php">📅 Citas</a>
        <a href="mascotas.php">🐶 Mascotas</a>
        <a href="historia_clinica.php">📋 Historia Clínica</a>
        <a href="clientes.php">👥 Clientes</a>
        <a href="inventario.php">📦 Inventario</a>
        <a class="active" href="reportes.php">📄 Reportes</a>
        <a href="configuracion.php">⚙️ Configuración</a>
    </nav>

    <a class="logout" href="logout.php">↩ Cerrar Sesión</a>

</aside>

<!-- CONTENIDO -->
<main class="main-content">

<header class="topbar">

    <h2>Reportes</h2>

    <a class="btn-nuevo" href="generar_reporte.php">
        + Generar Reporte
    </a>

</header>

<section class="cards">

    <a class="card card-link" href="generar_reporte.php?tipo=citas">

        <div>
            <p>Reporte de Citas</p>
            <h2>12</h2>
            <span>Generar</span>
        </div>

        <div class="icon blue">
            📅
        </div>

    </a>

    <a class="card card-link" href="generar_reporte.php?tipo=clientes">

        <div>
            <p>Reporte de Clientes</p>
            <h2>5</h2>
            <span>Generar</span>
        </div>

        <div class="icon green">
            👥
        </div>

    </a>

    <a class="card card-link" href="generar_reporte.php?tipo=mascotas">

        <div>
            <p>Reporte de Mascotas</p>
            <h2>8</h2>
            <span>Generar</span>
        </div>

        <div class="icon orange">
            🐶
        </div>

    </a>

    <a class="card card-link" href="generar_reporte.php?tipo=inventario">

        <div>
            <p>Reporte de Inventario</p>
            <h2>25</h2>
            <span>Generar</span>
        </div>

        <div class="icon purple">
            📦
        </div>

    </a>

</section>

</main>

</div>

</body>
</html>