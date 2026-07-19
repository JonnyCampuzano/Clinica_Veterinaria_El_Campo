<?php session_start(); if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit; } ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Producto</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app">



<main class="main-content">
<header class="topbar"><h2>Nuevo Producto</h2></header>

<section class="table-section">
<div class="table-header">
<h2>Registrar Producto</h2>
<a class="btn-nuevo" href="inventario.php">← Volver</a>
</div>

<form class="formulario">
<label>Nombre del producto</label>
<input type="text" placeholder="Ej: Vacuna antirrábica" required>

<label>Categoría</label>
<input type="text" placeholder="Ej: Vacunas, Medicamentos, Higiene" required>

<label>Stock</label>
<input type="number" placeholder="Ej: 25" required>

<label>Precio</label>
<input type="number" step="0.01" placeholder="Ej: 8.00" required>

<label>Estado</label>
<select required>
<option>Disponible</option>
<option>Agotado</option>
<option>Bajo stock</option>
</select>

<button type="submit">Guardar Producto</button>
</form>
</section>
</main>
</div>
</body>
</html>