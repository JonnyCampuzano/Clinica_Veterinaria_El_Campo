<?php session_start(); if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit; } ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Cliente</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app">



<main class="main-content">
<header class="topbar"><h2>Nuevo Cliente</h2></header>

<section class="table-section">
<div class="table-header">
<h2>Registrar Cliente</h2>
<a href="nueva_historia.php" class="btn-nuevo">
    + Nuevo registro
</a>
</div>

<form class="formulario">
<label>Nombre completo</label>
<input type="text" placeholder="Ej: Juan Pérez" required>

<label>Cédula</label>
<input type="text" placeholder="Ej: 0923456789" required>

<label>Teléfono</label>
<input type="text" placeholder="Ej: 0987654321" required>

<label>Correo</label>
<input type="email" placeholder="Ej: cliente@gmail.com" required>

<label>Dirección</label>
<textarea placeholder="Ingrese la dirección"></textarea>

<button type="submit">Guardar Cliente</button>
</form>
</section>
</main>
</div>
</body>
</html>