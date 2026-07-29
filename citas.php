<?php
session_start();

require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login.php");
    exit;
}

$buscar = trim($_GET["buscar"] ?? "");

if ($buscar !== "") {

    $consulta = $conexion->prepare(
        "SELECT id, fecha, hora, paciente, propietario, motivo, estado
         FROM citas
         WHERE paciente LIKE ?
            OR propietario LIKE ?
            OR motivo LIKE ?
         ORDER BY fecha ASC, hora ASC"
    );

    if (!$consulta) {
        die("Error al preparar la búsqueda: " . $conexion->error);
    }

    $termino = "%" . $buscar . "%";

    $consulta->bind_param(
        "sss",
        $termino,
        $termino,
        $termino
    );

    $consulta->execute();
    $resultado = $consulta->get_result();

} else {

    $resultado = $conexion->query(
        "SELECT id, fecha, hora, paciente, propietario, motivo, estado
         FROM citas
         ORDER BY fecha ASC, hora ASC"
    );
}

if (!$resultado) {
    die("Error al consultar las citas: " . $conexion->error);
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

    <title>Citas</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f7fc;
            color: #001b3d;
        }

        .main-content {
            flex: 1;
            min-width: 0;
        }

        .citas-section {
            padding: 30px;
        }

        .citas-contenedor {
            padding: 30px;
            background: #ffffff;
            border: 1px solid #dce8f7;
            border-radius: 20px;
            box-shadow: 0 10px 28px rgba(0, 27, 61, 0.08);
        }

        .citas-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .citas-header h2 {
            margin: 0;
            font-size: 27px;
        }

        .btn-nuevo {
            padding: 13px 21px;
            background: #2563eb;
            color: white;
            border-radius: 11px;
            text-decoration: none;
            font-weight: bold;
            white-space: nowrap;
        }

        .btn-nuevo:hover {
            background: #1d4ed8;
        }

        .mensaje {
            margin-bottom: 20px;
            padding: 14px 17px;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            border-radius: 11px;
            font-weight: bold;
        }

        .form-busqueda {
            display: flex;
            gap: 12px;
            margin-bottom: 25px;
        }

        .form-busqueda input {
            flex: 1;
            min-width: 0;
            padding: 13px 15px;
            border: 1px solid #cbd8e8;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 15px;
            outline: none;
        }

        .form-busqueda input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .btn-buscar,
        .btn-limpiar {
            padding: 13px 20px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-buscar {
            background: #2563eb;
            color: white;
        }

        .btn-limpiar {
            background: #64748b;
            color: white;
        }

        .tabla-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px 12px;
            border-bottom: 1px solid #dbe5f1;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #eff6ff;
            color: #001b3d;
        }

        .estado {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .estado-pendiente {
            background: #fef3c7;
            color: #92400e;
        }

        .estado-confirmada {
            background: #dcfce7;
            color: #166534;
        }

        .estado-cancelada {
            background: #fee2e2;
            color: #991b1b;
        }

        .acciones {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-editar,
        .btn-eliminar {
            padding: 9px 13px;
            border: none;
            border-radius: 9px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-editar {
            background: #f59e0b;
        }

        .btn-eliminar {
            background: #dc2626;
        }

        .form-eliminar {
            margin: 0;
        }

        .sin-registros {
            padding: 30px;
            background: #f8fafc;
            border: 1px dashed #94a3b8;
            border-radius: 12px;
            text-align: center;
        }

        @media (max-width: 800px) {
            .citas-section {
                padding: 15px;
            }

            .citas-header,
            .form-busqueda {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-nuevo,
            .btn-limpiar {
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
            <a href="../dashboard.php">
                📊 Dashboard
            </a>

            <a class="active" href="citas.php">
                📅 Citas
            </a>

            <a href="../mascotas.php">
                🐶 Mascotas
            </a>

            <a href="../historia_clinica.php">
                📋 Historia Clínica
            </a>

            <a href="../clientes.php">
                👥 Clientes
            </a>

            <a href="../inventario.php">
                📦 Inventario
            </a>

            <a href="../reportes.php">
                📄 Reportes
            </a>

            <a href="../configuracion.php">
                ⚙️ Configuración
            </a>
        </nav>

        <a class="logout" href="../logout.php">
            ↩ Cerrar sesión
        </a>

    </aside>

    <main class="main-content">

        <header class="topbar">
            <h2>Gestión de Citas</h2>
        </header>

        <section class="citas-section">

            <div class="citas-contenedor">

                <div class="citas-header">

                    <h2>Listado de Citas</h2>

                    <a
                        class="btn-nuevo"
                        href="nueva_citas.php"
                    >
                        + Nueva Cita
                    </a>

                </div>

                <?php if (isset($_GET["mensaje"])): ?>

                    <div class="mensaje">

                        <?php if ($_GET["mensaje"] === "registrada"): ?>
                            Cita registrada correctamente.
                        <?php elseif ($_GET["mensaje"] === "actualizada"): ?>
                            Cita actualizada correctamente.
                        <?php elseif ($_GET["mensaje"] === "eliminada"): ?>
                            Cita eliminada correctamente.
                        <?php endif; ?>

                    </div>

                <?php endif; ?>

                <form
                    class="form-busqueda"
                    method="GET"
                    action="citas.php"
                >

                    <input
                        type="text"
                        name="buscar"
                        placeholder="Buscar paciente, propietario o motivo..."
                        value="<?= htmlspecialchars($buscar) ?>"
                    >

                    <button
                        type="submit"
                        class="btn-buscar"
                    >
                        🔍 Buscar
                    </button>

                    <?php if ($buscar !== ""): ?>

                        <a
                            href="citas.php"
                            class="btn-limpiar"
                        >
                            Limpiar
                        </a>

                    <?php endif; ?>

                </form>

                <?php if ($resultado->num_rows > 0): ?>

                    <div class="tabla-responsive">

                        <table>

                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Propietario</th>
                                    <th>Motivo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php while ($cita = $resultado->fetch_assoc()): ?>

                                <?php
                                $claseEstado = "estado-pendiente";

                                if ($cita["estado"] === "Confirmada") {
                                    $claseEstado = "estado-confirmada";
                                }

                                if ($cita["estado"] === "Cancelada") {
                                    $claseEstado = "estado-cancelada";
                                }
                                ?>

                                <tr>

                                    <td>
                                        <?= date(
                                            "d/m/Y",
                                            strtotime($cita["fecha"])
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            "H:i",
                                            strtotime($cita["hora"])
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($cita["paciente"]) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($cita["propietario"]) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($cita["motivo"]) ?>
                                    </td>

                                    <td>
                                        <span class="estado <?= $claseEstado ?>">
                                            <?= htmlspecialchars($cita["estado"]) ?>
                                        </span>
                                    </td>

                                    <td>

                                        <div class="acciones">

                                            <a
                                                class="btn-editar"
                                                href="editar_citas.php?id=<?= (int) $cita["id"] ?>"
                                            >
                                                ✏️ Editar
                                            </a>

                                            <form
                                                class="form-eliminar"
                                                action="eliminar_citas.php"
                                                method="POST"
                                                onsubmit="return confirm('¿Seguro que deseas eliminar esta cita?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $cita["id"] ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn-eliminar"
                                                >
                                                    🗑️ Eliminar
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="sin-registros">
                        No se encontraron citas registradas.
                    </div>

                <?php endif; ?>

            </div>

        </section>

    </main>

</div>

</body>
</html>