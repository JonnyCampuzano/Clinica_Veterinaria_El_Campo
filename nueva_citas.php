<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nueva cita</title>

    <link rel="stylesheet" href="dashboard.css">

    <style>
        .main-content {
            flex: 1;
            min-height: 100vh;
            padding: 30px;
            background: #f3f7fc;
        }

        .formulario {
            max-width: 650px;
            margin: auto;
            padding: 30px;
            background: white;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0, 27, 61, 0.1);
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .form-header h2 {
            margin: 0;
        }

        .form-grupo {
            margin-bottom: 17px;
        }

        .form-grupo label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        .form-grupo input,
        .form-grupo textarea,
        .form-grupo select {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd8e8;
            border-radius: 9px;
            box-sizing: border-box;
            font-size: 15px;
        }

        .form-grupo input:focus,
        .form-grupo textarea:focus,
        .form-grupo select:focus {
            border-color: #2563eb;
            outline: none;
        }

        .form-grupo textarea {
            min-height: 100px;
            resize: vertical;
        }

        .fila {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn-volver,
        .btn-guardar {
            padding: 12px 18px;
            border: none;
            border-radius: 9px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-volver {
            background: #64748b;
        }

        .btn-guardar {
            width: 100%;
            background: #2563eb;
            font-size: 16px;
        }

        .btn-guardar:hover {
            background: #1d4ed8;
        }

        @media (max-width: 650px) {
            .main-content {
                padding: 15px;
            }

            .fila {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-header {
                flex-direction: column;
                align-items: flex-start;
            }
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
            <a class="active" href="citas.php">📅 Citas</a>
            <a href="mascotas.php">🐶 Mascotas</a>
            <a href="historia_clinica.php">📋 Historia Clínica</a>
            <a href="clientes.php">👥 Clientes</a>
            <a href="inventario.php">📦 Inventario</a>
            <a href="generar_reporte.php">📄 Reportes</a>
            <a href="configuracion.php">⚙️ Configuración</a>
        </nav>

        <a class="logout" href="logout.php">
            ↩ Cerrar sesión
        </a>

    </aside>

    <main class="main-content">

        <section class="formulario">

            <div class="form-header">
                <h2>Registrar nueva cita</h2>

                <a href="citas.php" class="btn-volver">
                    ← Volver
                </a>
            </div>

            <form action="guardar_citas.php" method="POST">

                <div class="form-grupo">
                    <label for="paciente">Paciente o mascota</label>

                    <input
                        type="text"
                        id="paciente"
                        name="paciente"
                        placeholder="Ejemplo: Max"
                        maxlength="100"
                        required
                    >
                </div>

                <div class="form-grupo">
                    <label for="propietario">Propietario</label>

                    <input
                        type="text"
                        id="propietario"
                        name="propietario"
                        placeholder="Ejemplo: Juan Pérez"
                        maxlength="100"
                        required
                    >
                </div>

                <div class="fila">

                    <div class="form-grupo">
                        <label for="fecha">Fecha</label>

                        <input
                            type="date"
                            id="fecha"
                            name="fecha"
                            value="<?= date('Y-m-d') ?>"
                            min="<?= date('Y-m-d') ?>"
                            required
                        >
                    </div>

                    <div class="form-grupo">
                        <label for="hora">Hora</label>

                        <input
                            type="time"
                            id="hora"
                            name="hora"
                            required
                        >
                    </div>

                </div>

                <div class="form-grupo">
                    <label for="motivo">Motivo de la consulta</label>

                    <textarea
                        id="motivo"
                        name="motivo"
                        placeholder="Ingrese el motivo de la cita"
                        maxlength="500"
                        required
                    ></textarea>
                </div>

                <div class="form-grupo">
                    <label for="estado">Estado</label>

                    <select id="estado" name="estado" required>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Confirmada">Confirmada</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>

                <button type="submit" class="btn-guardar">
                    💾 Guardar cita
                </button>

            </form>

        </section>

    </main>

</div>

</body>
</html>