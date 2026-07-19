<?php session_start(); if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit; } ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historia Clínica</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app">
<aside class="sidebar">
<div class="sidebar-logo"><div class="logo-box">🐾</div><div><h2>SISTEMA</h2><p>VETERINARIO</p></div></div>
<nav>
<a href="dashboard.php">📊 Dashboard</a>
<a href="citas.php">📅 Citas</a>
<a href="mascotas.php">🐶 Mascotas</a>
<a class="active" href="historia_clinica.php">📋 Historia Clínica</a>
<a href="clientes.php">👥 Clientes</a>
<a href="inventario.php">📦 Inventario</a>
<a href="reportes.php">📄 Reportes</a>
<a href="configuracion.php">⚙️ Configuración</a>
</nav>
<a class="logout" href="logout.php">↩ Cerrar Sesión</a>
</aside>

<main class="main-content">
<header class="topbar"><h2>Historia Clínica</h2></header>
<section class="table-section">
<div class="table-header">
    <h2>Registros Clínicos</h2>
    <a class="btn-nuevo" href="nueva_historia.php">
        + Nuevo Registro
    </a>
</div>
</a>
</div>
<table>
<thead><tr><th>Mascota</th><th>Fecha</th><th>Diagnóstico</th><th>Tratamiento</th><th>Veterinario</th></tr></thead>
<tbody>
<tr><td>Firulais</td><td>20/06/2026</td><td>Revisión general</td><td>Vitaminas</td><td>Dr. Carlos</td></tr>
<tr><td>Luna</td><td>20/06/2026</td><td>Vacunación</td><td>Vacuna triple felina</td><td>Dr. Carlos</td></tr>
</tbody>
</table>
</section>
</main>
</div>
</body>
</html>