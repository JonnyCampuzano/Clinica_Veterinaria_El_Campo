<?php session_start(); if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit; } ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inventario</title>
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
<a href="clientes.php">👥 Clientes</a>
<a class="active" href="inventario.php">📦 Inventario</a>
<a href="reportes.php">📄 Reportes</a>
<a href="configuracion.php">⚙️ Configuración</a>
</nav>
<a class="logout" href="logout.php">↩ Cerrar Sesión</a>
</aside>

<main class="main-content">
<header class="topbar"><h2>Inventario</h2></header>
<section class="table-section">
<div class="table-header">
    <h2>Productos y Medicamentos</h2>
    <a class="btn-nuevo" href="nuevo_producto.php">
        + Nuevo Producto
    </a>
</div>
<table>
<thead><tr><th>Producto</th><th>Categoría</th><th>Stock</th><th>Precio</th><th>Estado</th></tr></thead>
<tbody>
<tr><td>Vacuna antirrábica</td><td>Vacunas</td><td>25</td><td>$8.00</td><td>Disponible</td></tr>
<tr><td>Shampoo medicado</td><td>Higiene</td><td>12</td><td>$6.50</td><td>Disponible</td></tr>
</tbody>
</table>
</section>
</main>
</div>
</body>
</html>