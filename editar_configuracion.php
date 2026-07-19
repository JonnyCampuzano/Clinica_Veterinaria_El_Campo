<?php session_start(); if (!isset($_SESSION['usuario'])) { header("Location: login.php"); exit; } ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Configuración</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app">



<main class="main-content">
<header class="topbar"><h2>Editar Configuración</h2></header>

<section class="table-section">
<div class="table-header">
<h2>Datos del Sistema</h2>
<a class="btn-nuevo" href="configuracion.php">← Volver</a>
</div>

<form class="formulario">
<label>Nombre del sistema</label>
<input type="text" value="Sistema Veterinario" required>

<label>Nombre del centro veterinario</label>
<input type="text" value="El Campo" required>

<label>Correo institucional</label>
<input type="email" value="admin@elcampo.com" required>

<label>Teléfono</label>
<input type="text" value="0987654321" required>

<button type="submit">Guardar Configuración</button>
</form>
</section>
</main>
</div>
</body>
</html>