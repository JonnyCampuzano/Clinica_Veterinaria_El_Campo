<?php session_start(); if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit; } ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Clientes</title>
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
<a href="historia_clinica.php">📋 Historia Clínica</a>
<a class="active" href="clientes.php">👥 Clientes</a>
<a href="inventario.php">📦 Inventario</a>
<a href="reportes.php">📄 Reportes</a>
<a href="configuracion.php">⚙️ Configuración</a>
</nav>
<a class="logout" href="logout.php">↩ Cerrar Sesión</a>
</aside>

<main class="main-content">
<header class="topbar"><h2>Clientes</h2></header>
<section class="table-section">
<div class="table-header">
    <h2>Listado de Clientes</h2>
    <a class="btn-nuevo" href="nuevo_cliente.php">
        + Nuevo Cliente
    </a>
</div>
<table>
<thead><tr><th>Nombre</th><th>Cédula</th><th>Teléfono</th><th>Correo</th><th>Dirección</th></tr></thead>
<tbody>
<tr><td>Juan Pérez</td><td>0923456789</td><td>0987654321</td><td>juan@gmail.com</td><td>Guayaquil</td></tr>
<tr><td>María Gómez</td><td>0912345678</td><td>0991122334</td><td>maria@gmail.com</td><td>Nobol</td></tr>
</tbody>
</table>
</section>
</main>
</div>
</body>
</html>