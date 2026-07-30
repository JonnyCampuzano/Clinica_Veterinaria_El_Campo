<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: citas.php');
    exit;
}

$consulta = $conexion->prepare(
    'SELECT id, paciente, propietario, fecha, hora, motivo, estado
     FROM citas
     WHERE id = ?'
);

if (!$consulta) {
    die('Error en la consulta: ' . $conexion->error);
}

$consulta->bind_param('i', $id);
$consulta->execute();

$cita = $consulta->get_result()->fetch_assoc();

if (!$cita) {
    header('Location: citas.php');
    exit;
}

$estados = ['Pendiente', 'Confirmada', 'Cancelada'];

function escapar($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Editar cita</title>

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
        .btn-actualizar {
            padding: 12px 18px;
            border: none;
            border-radius: 9px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-volver {
            background: #2563eb;
        }

        .btn-actualizar {
            width: 100%;
            background: #16a34a;
            font-size: 16px;
        }

        .btn-actualizar:hover {
            background: #15803d;
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
                align-items: flex-start;
                flex-direction: column;
                gap: 15px;
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

                <h2>Editar cita</h2>

                <a class="btn-volver" href="citas.php">
                    ← Volver
                </a>

            </div>

            <form action="actualizar_cita.php" method="POST">

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) $cita['id'] ?>"
                >

                <div class="form-grupo">

                    <label for="paciente">Paciente o mascota</label>

                    <input
                        type="text"
                        id="paciente"
                        name="paciente"
                        value="<?= escapar($cita['paciente']) ?>"
                        required
                    >

                </div>

                <div class="form-grupo">

                    <label for="propietario">Propietario</label>

                    <input
                        type="text"
                        id="propietario"
                        name="propietario"
                        value="<?= escapar($cita['propietario']) ?>"
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
                            value="<?= escapar($cita['fecha']) ?>"
                            required
                        >

                    </div>

                    <div class="form-grupo">

                        <label for="hora">Hora</label>

                        <input
                            type="time"
                            id="hora"
                            name="hora"
                            value="<?= escapar(substr($cita['hora'], 0, 5)) ?>"
                            required
                        >

                    </div>

                </div>

                <div class="form-grupo">

                    <label for="motivo">Motivo de la consulta</label>

                    <textarea
                        id="motivo"
                        name="motivo"
                        required
                    ><?= escapar($cita['motivo']) ?></textarea>

                </div>

                <div class="form-grupo">

                    <label for="estado">Estado</label>

                    <select id="estado" name="estado" required>

                        <?php foreach ($estados as $estado): ?>

                            <option
                                value="<?= escapar($estado) ?>"
                                <?= $cita['estado'] === $estado ? 'selected' : '' ?>
                            >
                                <?= escapar($estado) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <button type="submit" class="btn-actualizar">
                    🔄 Actualizar cita
                </button>

            </form>

        </section>

    </main>

</div>

</body>
</html>