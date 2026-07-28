<?php
session_start();
require_once __DIR__ . "/config/conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

$error = "";

$nombre = "";
$cedula = "";
$telefono = "";
$correo = "";
$direccion = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $cedula = trim($_POST["cedula"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $direccion = trim($_POST["direccion"] ?? "");

    if (
        $nombre === "" ||
        $cedula === "" ||
        $telefono === "" ||
        $correo === "" ||
        $direccion === ""
    ) {
        $error = "Todos los campos son obligatorios.";

    } elseif (!preg_match("/^[0-9]{10}$/", $cedula)) {
        $error = "La cédula debe contener exactamente 10 números.";

    } elseif (!preg_match("/^[0-9]{10}$/", $telefono)) {
        $error = "El teléfono debe contener exactamente 10 números.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Ingrese un correo electrónico válido.";

    } else {

        /* Comprobar si la cédula ya está registrada */
        $verificar = $conexion->prepare(
            "SELECT id
             FROM clientes
             WHERE cedula = ?
             LIMIT 1"
        );

        if (!$verificar) {
            $error = "Error al verificar el cliente: " . $conexion->error;

        } else {

            $verificar->bind_param("s", $cedula);
            $verificar->execute();
            $resultadoVerificacion = $verificar->get_result();

            if ($resultadoVerificacion->num_rows > 0) {
                $error = "Ya existe un cliente registrado con esa cédula.";

            } else {

                $consulta = $conexion->prepare(
                    "INSERT INTO clientes
                    (nombre, cedula, telefono, correo, direccion)
                    VALUES (?, ?, ?, ?, ?)"
                );

                if (!$consulta) {
                    $error = "Error al preparar el registro: "
                        . $conexion->error;

                } else {

                    $consulta->bind_param(
                        "sssss",
                        $nombre,
                        $cedula,
                        $telefono,
                        $correo,
                        $direccion
                    );

                    if ($consulta->execute()) {
                        header("Location: clientes.php?mensaje=registrado");
                        exit;
                    }

                    $error = "No se pudo guardar el cliente: "
                        . $consulta->error;
                }
            }
        }
    }
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

    <title>Registrar Cliente</title>

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
            min-width: 0;
            padding: 20px;
        }

        .clientes-section {
            display: flex;
            justify-content: center;
            padding: 15px;
        }

        .clientes-contenedor {
            width: 100%;
            max-width: 500px;
            padding: 25px;
            background: #ffffff;
            border: 1px solid #dce8f7;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0, 27, 61, 0.08);
        }

        .clientes-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 22px;
        }

        .clientes-header h2 {
            margin: 0;
            font-size: 25px;
            color: #001b3d;
        }

        .btn-volver {
            padding: 11px 17px;
            background: #2563eb;
            color: #ffffff;
            border-radius: 10px;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            white-space: nowrap;
        }

        .btn-volver:hover {
            background: #1d4ed8;
        }

        .mensaje-error {
            margin-bottom: 18px;
            padding: 13px;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            font-weight: bold;
        }

        .form-grupo {
            display: flex;
            flex-direction: column;
            margin-bottom: 14px;
        }

        .form-grupo label {
            margin-bottom: 6px;
            color: #001b3d;
            font-size: 15px;
            font-weight: bold;
        }

        .form-grupo input,
        .form-grupo textarea {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #cbd8e8;
            border-radius: 10px;
            box-sizing: border-box;
            outline: none;
            font-size: 15px;
        }

        .form-grupo input:focus,
        .form-grupo textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .form-grupo textarea {
            min-height: 70px;
            resize: vertical;
        }

        .btn-guardar {
            width: 100%;
            margin-top: 5px;
            padding: 12px;
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-guardar:hover {
            background: #1d4ed8;
        }

        @media (max-width: 650px) {
            .main-content {
                padding: 10px;
            }

            .clientes-section {
                padding: 5px;
            }

            .clientes-contenedor {
                padding: 20px;
            }

            .clientes-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-volver {
                width: 100%;
                box-sizing: border-box;
                text-align: center;
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
            <a href="citas.php">📅 Citas</a>
            <a href="mascotas.php">🐶 Mascotas</a>
            <a href="historia_clinica.php">📋 Historia Clínica</a>
            <a class="active" href="clientes.php">👥 Clientes</a>
            <a href="inventario.php">📦 Inventario</a>
            <a href="reportes.php">📄 Reportes</a>
            <a href="configuracion.php">⚙️ Configuración</a>
        </nav>

        <a class="logout" href="logout.php">
            ↩ Cerrar sesión
        </a>

    </aside>

    <main class="main-content">

        <header class="topbar">
            <h2>Clientes</h2>
        </header>

        <section class="clientes-section">

            <div class="clientes-contenedor">

                <div class="clientes-header">

                    <h2>Registrar Cliente</h2>

                    <a class="btn-volver" href="clientes.php">
                        ← Volver
                    </a>

                </div>

                <?php if ($error !== ""): ?>

                    <div class="mensaje-error">
                        <?= htmlspecialchars($error) ?>
                    </div>

                <?php endif; ?>

                <!-- Se procesa en este mismo archivo -->
                <form method="POST">

                    <div class="form-grupo">

                        <label for="nombre">
                            Nombre completo
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            placeholder="Ej: Juan Pérez"
                            value="<?= htmlspecialchars($nombre) ?>"
                            required
                        >

                    </div>

                    <div class="form-grupo">

                        <label for="cedula">
                            Cédula
                        </label>

                        <input
                            type="text"
                            id="cedula"
                            name="cedula"
                            placeholder="Ej: 0923456789"
                            value="<?= htmlspecialchars($cedula) ?>"
                            maxlength="10"
                            inputmode="numeric"
                            pattern="[0-9]{10}"
                            required
                        >

                    </div>

                    <div class="form-grupo">

                        <label for="telefono">
                            Teléfono
                        </label>

                        <input
                            type="text"
                            id="telefono"
                            name="telefono"
                            placeholder="Ej: 0987654321"
                            value="<?= htmlspecialchars($telefono) ?>"
                            maxlength="10"
                            inputmode="numeric"
                            pattern="[0-9]{10}"
                            required
                        >

                    </div>

                    <div class="form-grupo">

                        <label for="correo">
                            Correo
                        </label>

                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            placeholder="Ej: cliente@gmail.com"
                            value="<?= htmlspecialchars($correo) ?>"
                            required
                        >

                    </div>

                    <div class="form-grupo">

                        <label for="direccion">
                            Dirección
                        </label>

                        <textarea
                            id="direccion"
                            name="direccion"
                            placeholder="Ingrese la dirección"
                            required
                        ><?= htmlspecialchars($direccion) ?></textarea>

                    </div>

                    <button type="submit" class="btn-guardar">
                        Guardar Cliente
                    </button>

                </form>

            </div>

        </section>

    </main>

</div>

</body>
</html>