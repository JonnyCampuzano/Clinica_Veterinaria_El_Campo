<?php
session_start();

require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit;
}

$id = (int) ($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: citas.php");
    exit;
}

$consulta = $conexion->prepare(
    "SELECT id, paciente, propietario, fecha, hora, motivo, estado
     FROM citas
     WHERE id = ?"
);

if (!$consulta) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$consulta->bind_param("i", $id);
$consulta->execute();

$resultado = $consulta->get_result();
$cita = $resultado->fetch_assoc();

if (!$cita) {
    header("Location: citas.php");
    exit;
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

    <title>Editar Cita</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <style>
        .main-content {
            flex: 1;
            padding: 25px;
            background: #f3f7fc;
        }

        .formulario {
            max-width: 600px;
            margin: 0 auto;
            padding: 28px;
            background: white;
            border: 1px solid #dce8f7;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0, 27, 61, 0.08);
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

        .btn-volver {
            padding: 11px 17px;
            background: #2563eb;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .form-grupo {
            margin-bottom: 16px;
        }

        .form-grupo label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        .form-grupo input,
        .form-grupo select,
        .form-grupo textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd8e8;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 15px;
            outline: none;
        }

        .form-grupo textarea {
            min-height: 90px;
            resize: vertical;
        }

        .fila {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn-actualizar {
            width: 100%;
            padding: 13px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-actualizar:hover {
            background: #15803d;
        }

        @media (max-width: 650px) {
            .fila {
                grid-template-columns: 1fr;
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
            <a href="../dashboard.php">📊 Dashboard</a>
            <a class="active" href="citas.php">📅 Citas</a>
            <a href="../mascotas.php">🐶 Mascotas</a>
            <a href="../historia_clinica.php">📋 Historia Clínica</a>
            <a href="../clientes.php">👥 Clientes</a>
            <a href="../inventario.php">📦 Inventario</a>
            <a href="../reportes.php">📄 Reportes</a>
            <a href="../configuracion.php">⚙️ Configuración</a>
        </nav>

        <a class="logout" href="../logout.php">
            ↩ Cerrar sesión
        </a>

    </aside>

    <main class="main-content">

        <section class="formulario">

            <div class="form-header">

                <h2>Editar Cita</h2>

                <a class="btn-volver" href="citas.php">
                    ← Volver
                </a>

            </div>

            <form
                action="actualizar_citas.php"
                method="POST"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) $cita["id"] ?>"
                >

                <div class="form-grupo">

                    <label for="paciente">
                        Paciente o mascota
                    </label>

                    <input
                        type="text"
                        id="paciente"
                        name="paciente"
                        value="<?= htmlspecialchars($cita["paciente"]) ?>"
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
                        value="<?= htmlspecialchars($cita["propietario"]) ?>"
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
                            value="<?= htmlspecialchars($cita["fecha"]) ?>"
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
                            value="<?= htmlspecialchars(substr($cita["hora"], 0, 5)) ?>"
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
                    ><?= htmlspecialchars($cita["motivo"]) ?></textarea>

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

                        <option
                            value="Pendiente"
                            <?= $cita["estado"] === "Pendiente" ? "selected" : "" ?>
                        >
                            Pendiente
                        </option>

                        <option
                            value="Confirmada"
                            <?= $cita["estado"] === "Confirmada" ? "selected" : "" ?>
                        >
                            Confirmada
                        </option>

                        <option
                            value="Cancelada"
                            <?= $cita["estado"] === "Cancelada" ? "selected" : "" ?>
                        >
                            Cancelada
                        </option>

                    </select>

                </div>

                <button
                    type="submit"
                    class="btn-actualizar"
                >
                    🔄 Actualizar Cita
                </button>

            </form>

        </section>

    </main>

</div>

</body>
</html>