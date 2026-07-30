<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

/* Buscar el archivo de conexión */
$rutasConexion = [
    __DIR__ . '/conexion.php',
    __DIR__ . '/database/conexion.php'
];

$conexionEncontrada = false;

foreach ($rutasConexion as $ruta) {
    if (file_exists($ruta)) {
        require_once $ruta;
        $conexionEncontrada = true;
        break;
    }
}

if (!$conexionEncontrada) {
    die('Error: no se encontró el archivo conexion.php.');
}

/* Obtener el ID de la cita */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: citas.php');
    exit;
}

/* Consultar la cita */
$consulta = $conexion->prepare(
    'SELECT id, paciente, propietario, fecha, hora, motivo, estado
     FROM citas
     WHERE id = ?'
);

if (!$consulta) {
    die('Error al preparar la consulta: ' . $conexion->error);
}

$consulta->bind_param('i', $id);
$consulta->execute();

$cita = $consulta->get_result()->fetch_assoc();

if (!$cita) {
    header('Location: citas.php');
    exit;
}

function escapar($valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

$estados = [
    'Pendiente',
    'Confirmada',
    'Cancelada'
];
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

    <link rel="stylesheet" href="dashboard.css?v=8">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f7fc;
            color: #0f172a;
        }

        .app {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ===========================
           BARRA LATERAL
        ============================ */

        .sidebar {
            width: 288px;
            min-width: 288px;
            min-height: 100vh;
            padding: 18px 0;
            display: flex;
            flex-direction: column;
            background: #123e73;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 0 16px;
            margin-bottom: 42px;
        }

        .logo-box {
            width: 64px;
            height: 64px;
            min-width: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #3478f6;
            border-radius: 18px;
            font-size: 27px;
        }

        .sidebar-logo h2 {
            margin: 0;
            color: white;
            font-size: 25px;
            line-height: 1;
            font-weight: 800;
        }

        .sidebar-logo p {
            margin: 8px 0 0;
            color: white;
            font-size: 15px;
        }

        .sidebar nav {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .sidebar nav a {
            width: 100%;
            min-height: 62px;
            padding: 17px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
            font-size: 18px;
            transition: background 0.2s ease;
        }

        .sidebar nav a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar nav a.active {
            background: #3478f6;
            color: white;
            border-radius: 18px;
        }

        .logout {
            margin-top: auto;
            padding: 20px 24px;
            color: white;
            text-decoration: none;
            font-size: 17px;
        }

        .logout:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* ===========================
           FORMULARIO
        ============================ */

        .main-content {
            flex: 1;
            min-width: 0;
            min-height: 100vh;
            padding: 30px;
            background: #f3f7fc;
        }

        .formulario {
            max-width: 650px;
            margin: auto;
            padding: 30px;
            background: white;
            border: 1px solid #dce8f7;
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
            color: #0f172a;
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
            padding: 12px 14px;
            border: 1px solid #cbd8e8;
            border-radius: 9px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            font-size: 15px;
            outline: none;
        }

        .form-grupo input:focus,
        .form-grupo textarea:focus,
        .form-grupo select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
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
            display: inline-block;
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

        .btn-volver:hover {
            background: #1d4ed8;
        }

        .btn-actualizar {
            width: 100%;
            background: #16a34a;
            font-size: 16px;
        }

        .btn-actualizar:hover {
            background: #15803d;
        }

        @media (max-width: 800px) {
            .sidebar {
                width: 240px;
                min-width: 240px;
            }

            .sidebar-logo h2 {
                font-size: 21px;
            }

            .sidebar nav a {
                padding: 15px 18px;
                font-size: 16px;
            }
        }

        @media (max-width: 700px) {
            .app {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-width: 100%;
                min-height: auto;
                padding-bottom: 20px;
            }

            .sidebar nav {
                padding: 0 10px;
            }

            .sidebar nav a {
                min-height: 52px;
                border-radius: 12px;
            }

            .logout {
                margin-top: 20px;
            }

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

            <div class="logo-box">
                🐾
            </div>

            <div>
                <h2>SISTEMA</h2>
                <p>VETERINARIO</p>
            </div>

        </div>

        <nav>

            <a href="dashboard.php">
                📊 Dashboard
            </a>

            <a class="active" href="citas.php">
                🗓️ Citas
            </a>

            <a href="mascotas.php">
                🐶 Mascotas
            </a>

            <a href="historia_clinica.php">
                📋 Historia Clínica
            </a>

            <a href="clientes.php">
                👥 Clientes
            </a>

            <a href="inventario.php">
                📦 Inventario
            </a>

            <a href="generar_reporte.php">
                📄 Reportes
            </a>

            <a href="configuracion.php">
                ⚙️ Configuración
            </a>

        </nav>

        <a class="logout" href="logout.php">
            ↩ Cerrar sesión
        </a>

    </aside>

    <main class="main-content">

        <section class="formulario">

            <div class="form-header">

                <h2>Editar cita</h2>

                <a
                    class="btn-volver"
                    href="citas.php"
                >
                    ← Volver
                </a>

            </div>

            <form
                action="actualizar_cita.php"
                method="POST"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) $cita['id'] ?>"
                >

                <div class="form-grupo">

                    <label for="paciente">
                        Paciente o mascota
                    </label>

                    <input
                        type="text"
                        id="paciente"
                        name="paciente"
                        value="<?= escapar($cita['paciente']) ?>"
                        required
                    >

                </div>

                <div class="form-grupo">

                    <label for="propietario">
                        Propietario
                    </label>

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

                        <label for="fecha">
                            Fecha
                        </label>

                        <input
                            type="date"
                            id="fecha"
                            name="fecha"
                            value="<?= escapar($cita['fecha']) ?>"
                            required
                        >

                    </div>

                    <div class="form-grupo">

                        <label for="hora">
                            Hora
                        </label>

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

                    <label for="motivo">
                        Motivo de la consulta
                    </label>

                    <textarea
                        id="motivo"
                        name="motivo"
                        required
                    ><?= escapar($cita['motivo']) ?></textarea>

                </div>

                <div class="form-grupo">

                    <label for="estado">
                        Estado
                    </label>

                    <select
                        id="estado"
                        name="estado"
                        required
                    >

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

                <button
                    type="submit"
                    class="btn-actualizar"
                >
                    🔄 Actualizar cita
                </button>

            </form>

        </section>

    </main>

</div>

</body>

</html>