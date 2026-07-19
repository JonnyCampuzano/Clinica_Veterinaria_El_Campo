<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$tipo = $_GET['tipo'] ?? 'general';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Generar Reporte</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="app">

<aside class="sidebar">
<div class="sidebar-logo">
<div class="logo-box">🐾</div>
<div><h2>SISTEMA</h2><p>VETERINARIO</p></div>
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

<main class="main-content">
<header class="topbar">
<h2>Generar Reporte</h2>
<a class="btn-nuevo" href="reportes.php">← Volver</a>
</header>

<section class="table-section">
<h2>Reporte seleccionado: <?php echo ucfirst($tipo); ?></h2>
<br>

<form class="formulario">
<label>Tipo de reporte</label>
<select required>
<option <?php if($tipo=="citas") echo "selected"; ?>>Reporte de Citas</option>
<option <?php if($tipo=="clientes") echo "selected"; ?>>Reporte de Clientes</option>
<option <?php if($tipo=="mascotas") echo "selected"; ?>>Reporte de Mascotas</option>
<option <?php if($tipo=="inventario") echo "selected"; ?>>Reporte de Inventario</option>
</select>

<label>Fecha inicio</label>
<input type="date" required>

<label>Fecha fin</label>
<input type="date" required>

<button type="submit">Generar Reporte</button>
</form>
</section>

</main>
</div>

</body>
</html>