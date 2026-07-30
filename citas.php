<?php

/* ===============================
   SESIÓN
================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}


/* ===============================
   CONEXIÓN
================================ */

// Busca conexion.php en cualquiera de estas dos ubicaciones.
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
    die(
        'Error: no se encontró el archivo conexion.php. ' .
        'Debe estar en la carpeta principal o dentro de database.'
    );
}

if (!isset($conexion)) {
    die('Error: el archivo de conexión no creó la variable $conexion.');
}


/* ===============================
   BÚSQUEDA DE CITAS
================================ */

$buscar = trim($_GET['buscar'] ?? '');

$sql = "
    SELECT
        id,
        fecha,
        hora,
        paciente,
        propietario,
        motivo,
        estado
    FROM citas
";

if ($buscar !== '') {
    $sql .= "
        WHERE paciente LIKE ?
           OR propietario LIKE ?
           OR motivo LIKE ?
    ";
}

$sql .= " ORDER BY fecha ASC, hora ASC";

$consulta = $conexion->prepare($sql);

if (!$consulta) {
    die('Error al preparar la consulta: ' . $conexion->error);
}

if ($buscar !== '') {
    $termino = '%' . $buscar . '%';

    $consulta->bind_param(
        'sss',
        $termino,
        $termino,
        $termino
    );
}

if (!$consulta->execute()) {
    die('Error al consultar las citas: ' . $consulta->error);
}

$resultado = $consulta->get_result();


/* ===============================
   MENSAJES
================================ */

$mensajes = [
    'registrada' => 'Cita registrada correctamente.',
    'actualizada' => 'Cita actualizada correctamente.',
    'eliminada' => 'Cita eliminada correctamente.'
];

$mensajeActual = $_GET['mensaje'] ?? '';


/* ===============================
   FUNCIÓN DE SEGURIDAD
================================ */

function escapar($valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
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

    <title>Gestión de citas</title>

    <!-- CSS general del proyecto -->
    <link rel="stylesheet" href="dashboard.css?v=5">

    <style>
        /* ===============================
           CONFIGURACIÓN GENERAL
        ================================ */

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


        /* ===============================
           BARRA LATERAL DE CITAS
        ================================ */

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
            border-radius: 0;
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


        /* ===============================
           CONTENIDO PRINCIPAL
        ================================ */

        .main-content {
            flex: 1;
            min-width: 0;
            min-height: 100vh;
            background: #f3f7fc;
        }

        .topbar {
            min-height: 82px;
            padding: 24px 32px;
            display: flex;
            align-items: center;
            background: white;
            border-bottom: 1px solid #dbe5f1;
        }

        .topbar h2 {
            margin: 0;
            color: #001b3d;
            font-size: 27px;
        }

        .citas-section {
            padding: 30px;
        }

        .citas-contenedor {
            padding: 30px;
            background: white;
            border: 1px solid #dce8f7;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(0, 27, 61, 0.08);
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
            color: #001b3d;
        }


        /* ===============================
           BOTONES
        ================================ */

        .btn {
            display: inline-block;
            padding: 11px 17px;
            border: none;
            border-radius: 9px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-nuevo,
        .btn-buscar {
            background: #2563eb;
        }

        .btn-nuevo:hover,
        .btn-buscar:hover {
            background: #1d4ed8;
        }

        .btn-limpiar {
            background: #64748b;
        }

        .btn-editar {
            background: #f59e0b;
        }

        .btn-eliminar {
            background: #dc2626;
        }


        /* ===============================
           MENSAJE
        ================================ */

        .mensaje {
            margin-bottom: 20px;
            padding: 14px 17px;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            border-radius: 10px;
            font-weight: bold;
        }


        /* ===============================
           BUSCADOR
        ================================ */

        .form-busqueda {
            display: flex;
            gap: 12px;
            margin-bottom: 25px;
        }

        .form-busqueda input {
            flex: 1;
            min-width: 0;
            padding: 12px 14px;
            border: 1px solid #cbd8e8;
            border-radius: 9px;
            font-size: 15px;
            outline: none;
        }

        .form-busqueda input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }


        /* ===============================
           TABLA
        ================================ */

        .tabla-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px 11px;
            border-bottom: 1px solid #dbe5f1;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #eff6ff;
            color: #001b3d;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .acciones {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-eliminar {
            margin: 0;
        }


        /* ===============================
           ESTADOS
        ================================ */

        .estado {
            display: inline-block;
            padding: 6px 10px;
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

        .sin-registros {
            padding: 30px;
            background: #f8fafc;
            border: 1px dashed #94a3b8;
            border-radius: 10px;
            text-align: center;
        }


        /* ===============================
           RESPONSIVO
        ================================ */

        @media (max-width: 900px) {
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

            .citas-section {
                padding: 15px;
            }

            .citas-contenedor {
                padding: 20px;
            }

            .citas-header,
            .form-busqueda {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                text-align: center;
            }
        }
    </style>

</head>

<body>

<div class="app">

    <!-- ===============================
         BARRA LATERAL
    ================================ -->

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


    <!-- ===============================
         CONTENIDO
    ================================ -->

    <main class="main-content">

        <header class="topbar">
            <h2>Gestión de citas</h2>
        </header>

        <section class="citas-section">

            <div class="citas-contenedor">

                <div class="citas-header">

                    <h2>Listado de citas</h2>

                    <a
                        href="nueva_citas.php"
                        class="btn btn-nuevo"
                    >
                        + Nueva cita
                    </a>

                </div>


                <!-- Mensaje de confirmación -->

                <?php if (isset($mensajes[$mensajeActual])): ?>

                    <div class="mensaje">
                        <?= escapar($mensajes[$mensajeActual]) ?>
                    </div>

                <?php endif; ?>


                <!-- Buscador -->

                <form
                    method="GET"
                    action="citas.php"
                    class="form-busqueda"
                >

                    <input
                        type="text"
                        name="buscar"
                        placeholder="Buscar paciente, propietario o motivo"
                        value="<?= escapar($buscar) ?>"
                    >

                    <button
                        type="submit"
                        class="btn btn-buscar"
                    >
                        🔍 Buscar
                    </button>

                    <?php if ($buscar !== ''): ?>

                        <a
                            href="citas.php"
                            class="btn btn-limpiar"
                        >
                            Limpiar
                        </a>

                    <?php endif; ?>

                </form>


                <!-- Tabla de citas -->

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
                                $clasesEstado = [
                                    'Pendiente' => 'estado-pendiente',
                                    'Confirmada' => 'estado-confirmada',
                                    'Cancelada' => 'estado-cancelada'
                                ];

                                $claseEstado =
                                    $clasesEstado[$cita['estado']]
                                    ?? 'estado-pendiente';
                                ?>

                                <tr>

                                    <td>
                                        <?= date(
                                            'd/m/Y',
                                            strtotime($cita['fecha'])
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            'H:i',
                                            strtotime($cita['hora'])
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= escapar($cita['paciente']) ?>
                                    </td>

                                    <td>
                                        <?= escapar($cita['propietario']) ?>
                                    </td>

                                    <td>
                                        <?= escapar($cita['motivo']) ?>
                                    </td>

                                    <td>
                                        <span
                                            class="estado <?= escapar($claseEstado) ?>"
                                        >
                                            <?= escapar($cita['estado']) ?>
                                        </span>
                                    </td>

                                    <td>

                                        <div class="acciones">

                                            <a
                                                href="editar_citas.php?id=<?= (int) $cita['id'] ?>"
                                                class="btn btn-editar"
                                            >
                                                ✏️ Editar
                                            </a>

                                            <form
                                                action="eliminar_citas.php"
                                                method="POST"
                                                class="form-eliminar"
                                                onsubmit="return confirm('¿Seguro que deseas eliminar esta cita?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $cita['id'] ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn btn-eliminar"
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