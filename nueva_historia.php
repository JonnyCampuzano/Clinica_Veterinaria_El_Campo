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
<title>Registrar Historia Clínica</title>
<link rel="stylesheet" href="assets/css/style.css">

<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #f3f7fc;
        color: #001b3d;
    }

    .app {
        display: flex;
        min-height: 100vh;
    }

    .main-content {
        flex: 1;
        padding: 35px;
    }

    .form-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 45px 50px;
        max-width: 850px;
        margin: 0 auto;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        border: 1px solid #dce8f7;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
    }

    .form-header h1 {
        font-size: 34px;
        margin: 0;
        color: #001533;
    }

    .btn-volver {
        background: #2563eb;
        color: #ffffff;
        padding: 16px 28px;
        border-radius: 14px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.25);
    }

    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 24px;
    }

    .form-group label {
        font-size: 22px;
        margin-bottom: 10px;
        color: #001b3d;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 18px 22px;
        border: 1px solid #cbd8e8;
        border-radius: 14px;
        font-size: 18px;
        outline: none;
        box-sizing: border-box;
        background: #ffffff;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    textarea {
        resize: vertical;
        min-height: 150px;
    }

    .btn-guardar {
        width: 100%;
        background: #2563eb;
        color: white;
        border: none;
        padding: 18px;
        border-radius: 14px;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 10px;
    }

    .btn-guardar:hover,
    .btn-volver:hover {
        background: #1d4ed8;
    }
</style>
</head>

<body>

<div class="app">

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
            <a class="active" href="historia_clinica.php">📋 Historia Clínica</a>
            <a href="clientes.php">👥 Clientes</a>
            <a href="inventario.php">📦 Inventario</a>
            <a href="reportes.php">📄 Reportes</a>
            <a href="configuracion.php">⚙️ Configuración</a>
        </nav>

        <a class="logout" href="logout.php">↩ Cerrar Sesión</a>
    </aside>

    <main class="main-content">

        <section class="form-card">

            <div class="form-header">
                <h1>Registrar Historia Clínica</h1>
                <a class="btn-volver" href="historia_clinica.php">← Volver</a>
            </div>

            <form action="#" method="POST">

                <div class="form-group">
                    <label for="mascota">Mascota</label>
                    <input type="text" id="mascota" name="mascota" placeholder="Nombre de la mascota" required>
                </div>

                <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" id="fecha" name="fecha" required>
                </div>

                <div class="form-group">
                    <label for="diagnostico">Diagnóstico</label>
                    <textarea id="diagnostico" name="diagnostico" placeholder="Ingrese el diagnóstico" required></textarea>
                </div>

                <div class="form-group">
                    <label for="tratamiento">Tratamiento</label>
                    <textarea id="tratamiento" name="tratamiento" placeholder="Ingrese el tratamiento" required></textarea>
                </div>

                <div class="form-group">
                    <label for="veterinario">Veterinario</label>
                    <input type="text" id="veterinario" name="veterinario" placeholder="Nombre del veterinario" required>
                </div>

                <button type="submit" class="btn-guardar">Guardar Historia</button>

            </form>

        </section>

    </main>

</div>

</body>
</html>