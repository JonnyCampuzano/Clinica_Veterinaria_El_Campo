<?php
session_start();

if (!isset($_SESSION["usuario"])) {
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
        .main-content {
            padding: 35px;
        }

        .form-card {
            max-width: 850px;
            margin: auto;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px #00000015;
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-group {
            margin-top: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #cbd8e8;
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 16px;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn-volver,
        .btn-guardar {
            background: #2563eb;
            color: white;
            padding: 14px 22px;
            border: none;
            border-radius: 12px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-guardar {
            width: 100%;
            margin-top: 25px;
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
            <a class="active" href="historia_clinica.php">
                📋 Historia Clínica
            </a>
            <a href="clientes.php">👥 Clientes</a>
            <a href="inventario.php">📦 Inventario</a>
            <a href="reportes.php">📄 Reportes</a>
            <a href="configuracion.php">⚙️ Configuración</a>
        </nav>

        <a class="logout" href="logout.php">
            ↩ Cerrar Sesión
        </a>

    </aside>

    <main class="main-content">

        <section class="form-card">

            <div class="form-header">
                <h1>Registrar Historia Clínica</h1>

                <a class="btn-volver" href="historia_clinica.php">
                    ← Volver
                </a>
            </div>

            <form action="guardar_historia.php" method="POST">

                <div class="form-group">
                    <label for="mascota">Mascota</label>
                    <input
                        type="text"
                        id="mascota"
                        name="mascota"
                        placeholder="Nombre de la mascota"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input
                        type="date"
                        id="fecha"
                        name="fecha"
                        value="<?= date("Y-m-d") ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="diagnostico">Diagnóstico</label>
                    <textarea
                        id="diagnostico"
                        name="diagnostico"
                        placeholder="Ingrese el diagnóstico"
                        required
                    ></textarea>
                </div>

                <div class="form-group">
                    <label for="tratamiento">Tratamiento</label>
                    <textarea
                        id="tratamiento"
                        name="tratamiento"
                        placeholder="Ingrese el tratamiento"
                        required
                    ></textarea>
                </div>

                <div class="form-group">
                    <label for="veterinario">Veterinario</label>
                    <input
                        type="text"
                        id="veterinario"
                        name="veterinario"
                        placeholder="Nombre del veterinario"
                        required
                    >
                </div>

                <button type="submit" class="btn-guardar">
                    Guardar Historia
                </button>

            </form>

        </section>

    </main>

</div>

</body>
</html>