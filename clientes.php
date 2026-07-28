<?php
session_start();
require_once __DIR__ . "/config/conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

$mensajeError = "";

/* =====================================================
   ACTUALIZAR CLIENTE
===================================================== */
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["actualizar_cliente"])
) {
    $id = (int) ($_POST["id"] ?? 0);
    $nombre = trim($_POST["nombre"] ?? "");
    $cedula = trim($_POST["cedula"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $direccion = trim($_POST["direccion"] ?? "");

    if (
        $id <= 0 ||
        $nombre === "" ||
        $cedula === "" ||
        $telefono === "" ||
        $correo === "" ||
        $direccion === ""
    ) {
        $mensajeError = "Todos los campos son obligatorios.";

    } elseif (!preg_match("/^[0-9]{10}$/", $cedula)) {
        $mensajeError = "La cédula debe contener exactamente 10 números.";

    } elseif (!preg_match("/^[0-9]{10}$/", $telefono)) {
        $mensajeError = "El teléfono debe contener exactamente 10 números.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensajeError = "El correo electrónico no es válido.";

    } else {
        try {
            $consulta = $conexion->prepare(
                "UPDATE clientes
                 SET nombre = ?,
                     cedula = ?,
                     telefono = ?,
                     correo = ?,
                     direccion = ?
                 WHERE id = ?"
            );

            $consulta->bind_param(
                "sssssi",
                $nombre,
                $cedula,
                $telefono,
                $correo,
                $direccion,
                $id
            );

            $consulta->execute();
            $consulta->close();

            header("Location: clientes.php?mensaje=actualizado");
            exit;

        } catch (Throwable $error) {
            $mensajeError = "No se pudo actualizar el cliente.";
        }
    }
}

/* =====================================================
   CONSULTAR CLIENTES
===================================================== */
try {
    $resultado = $conexion->query(
        "SELECT id, nombre, cedula, telefono, correo, direccion
         FROM clientes
         ORDER BY id DESC"
    );

} catch (Throwable $error) {
    die(
        "<div style='
            margin: 40px;
            padding: 20px;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 12px;
            font-family: Arial;
        '>
            No se pudieron cargar los clientes.
            Verifica que la tabla <strong>clientes</strong> exista.
        </div>"
    );
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

    <title>Clientes</title>

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
            background: #f3f7fc;
        }

        .topbar {
            padding: 24px 35px;
            background: #ffffff;
            border-bottom: 1px solid #dce8f7;
        }

        .topbar h2 {
            margin: 0;
            color: #001b3d;
        }

        .clientes-section {
            padding: 30px;
        }

        .clientes-contenedor {
            background: #ffffff;
            border: 1px solid #dce8f7;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 10px 28px rgba(0, 27, 61, 0.08);
        }

        .clientes-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .clientes-header h2 {
            margin: 0;
            font-size: 27px;
            color: #001b3d;
        }

        .btn-nuevo {
            display: inline-block;
            padding: 13px 20px;
            background: #2563eb;
            color: #ffffff;
            border-radius: 11px;
            text-decoration: none;
            font-weight: bold;
            white-space: nowrap;
            box-shadow: 0 7px 17px rgba(37, 99, 235, 0.25);
        }

        .btn-nuevo:hover {
            background: #1d4ed8;
        }

        .mensaje-exito {
            margin-bottom: 20px;
            padding: 14px 18px;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            border-radius: 11px;
            font-weight: bold;
        }

        .mensaje-error {
            margin-bottom: 20px;
            padding: 14px 18px;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            border-radius: 11px;
            font-weight: bold;
        }

        .cliente-form {
            max-width: 760px;
            margin: 0 auto 25px;
            padding: 24px;
            background: #f8fafc;
            border: 1px solid #dbe5f1;
            border-radius: 17px;
        }

        .cliente-form:last-child {
            margin-bottom: 0;
        }

        .cliente-form h3 {
            margin: 0 0 20px;
            color: #2563eb;
            font-size: 20px;
        }

        .fila-campos {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .form-grupo {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
        }

        .form-grupo.completo {
            grid-column: 1 / -1;
        }

        .form-grupo label {
            margin-bottom: 7px;
            color: #001b3d;
            font-size: 15px;
            font-weight: bold;
        }

        .form-grupo input,
        .form-grupo textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd8e8;
            border-radius: 10px;
            background: #ffffff;
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
            min-height: 75px;
            resize: vertical;
        }

        .acciones {
            display: flex;
            justify-content: flex-end;
        }

        .btn-actualizar {
            padding: 12px 22px;
            background: #16a34a;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-actualizar:hover {
            background: #15803d;
        }

        .sin-clientes {
            padding: 35px;
            background: #f8fafc;
            border: 1px dashed #94a3b8;
            border-radius: 14px;
            text-align: center;
            color: #475569;
        }

        @media (max-width: 800px) {
            .clientes-section {
                padding: 15px;
            }

            .clientes-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .fila-campos {
                grid-template-columns: 1fr;
            }

            .form-grupo.completo {
                grid-column: auto;
            }

            .btn-nuevo {
                width: 100%;
                box-sizing: border-box;
                text-align: center;
            }

            .acciones {
                justify-content: stretch;
            }

            .btn-actualizar {
                width: 100%;
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

                    <h2>Listado de Clientes</h2>

                    <a class="btn-nuevo" href="nuevo_cliente.php">
                        + Nuevo Cliente
                    </a>

                </div>

                <?php if (
                    isset($_GET["mensaje"]) &&
                    $_GET["mensaje"] === "actualizado"
                ): ?>

                    <div class="mensaje-exito">
                        Cliente actualizado correctamente.
                    </div>

                <?php endif; ?>

                <?php if (
                    isset($_GET["mensaje"]) &&
                    $_GET["mensaje"] === "registrado"
                ): ?>

                    <div class="mensaje-exito">
                        Cliente registrado correctamente.
                    </div>

                <?php endif; ?>

                <?php if ($mensajeError !== ""): ?>

                    <div class="mensaje-error">
                        <?= htmlspecialchars($mensajeError) ?>
                    </div>

                <?php endif; ?>

                <?php if ($resultado->num_rows > 0): ?>

                    <?php while ($cliente = $resultado->fetch_assoc()): ?>

                        <!-- Se actualiza en este mismo archivo -->
                        <form class="cliente-form" method="POST">

                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int) $cliente["id"] ?>"
                            >

                            <h3>
                                Cliente #<?= (int) $cliente["id"] ?>
                            </h3>

                            <div class="fila-campos">

                                <div class="form-grupo">

                                    <label for="nombre<?= (int) $cliente["id"] ?>">
                                        Nombre completo
                                    </label>

                                    <input
                                        type="text"
                                        id="nombre<?= (int) $cliente["id"] ?>"
                                        name="nombre"
                                        value="<?= htmlspecialchars($cliente["nombre"]) ?>"
                                        required
                                    >

                                </div>

                                <div class="form-grupo">

                                    <label for="cedula<?= (int) $cliente["id"] ?>">
                                        Cédula
                                    </label>

                                    <input
                                        type="text"
                                        id="cedula<?= (int) $cliente["id"] ?>"
                                        name="cedula"
                                        value="<?= htmlspecialchars($cliente["cedula"]) ?>"
                                        maxlength="10"
                                        inputmode="numeric"
                                        pattern="[0-9]{10}"
                                        required
                                    >

                                </div>

                                <div class="form-grupo">

                                    <label for="telefono<?= (int) $cliente["id"] ?>">
                                        Teléfono
                                    </label>

                                    <input
                                        type="text"
                                        id="telefono<?= (int) $cliente["id"] ?>"
                                        name="telefono"
                                        value="<?= htmlspecialchars($cliente["telefono"]) ?>"
                                        maxlength="10"
                                        inputmode="numeric"
                                        pattern="[0-9]{10}"
                                        required
                                    >

                                </div>

                                <div class="form-grupo">

                                    <label for="correo<?= (int) $cliente["id"] ?>">
                                        Correo
                                    </label>

                                    <input
                                        type="email"
                                        id="correo<?= (int) $cliente["id"] ?>"
                                        name="correo"
                                        value="<?= htmlspecialchars($cliente["correo"]) ?>"
                                        required
                                    >

                                </div>

                                <div class="form-grupo completo">

                                    <label for="direccion<?= (int) $cliente["id"] ?>">
                                        Dirección
                                    </label>

                                    <textarea
                                        id="direccion<?= (int) $cliente["id"] ?>"
                                        name="direccion"
                                        required
                                    ><?= htmlspecialchars($cliente["direccion"]) ?></textarea>

                                </div>

                            </div>

                            <div class="acciones">

                                <button
                                    type="submit"
                                    name="actualizar_cliente"
                                    class="btn-actualizar"
                                >
                                    🔄 Actualizar
                                </button>

                            </div>

                        </form>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="sin-clientes">
                        No existen clientes registrados.
                    </div>

                <?php endif; ?>

            </div>

        </section>

    </main>

</div>

</body>
</html>