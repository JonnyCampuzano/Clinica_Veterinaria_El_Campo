<?php
session_start();
require_once "config/conexion.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

/* Texto ingresado en el buscador */
$buscar = trim($_GET["buscar"] ?? "");

/* Consulta de historias clínicas */
if ($buscar !== "") {

    $consulta = $conexion->prepare(
        "SELECT id, mascota, fecha, diagnostico, tratamiento, veterinario
         FROM historias_clinicas
         WHERE mascota LIKE ?
         ORDER BY id DESC"
    );

    if (!$consulta) {
        die("Error al preparar la búsqueda: " . $conexion->error);
    }

    $termino = "%" . $buscar . "%";

    $consulta->bind_param("s", $termino);
    $consulta->execute();

    $resultado = $consulta->get_result();

} else {

    $resultado = $conexion->query(
        "SELECT id, mascota, fecha, diagnostico, tratamiento, veterinario
         FROM historias_clinicas
         ORDER BY id DESC"
    );
}

if (!$resultado) {
    die("Error al consultar las historias clínicas: " . $conexion->error);
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

    <title>Historia Clínica</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <style>
        .main-content {
            flex: 1;
            min-width: 0;
            background: #f3f7fc;
        }

        .topbar {
            background: #ffffff;
            padding: 28px 40px;
            border-bottom: 1px solid #dce8f7;
        }

        .topbar h2 {
            margin: 0;
            font-size: 30px;
            color: #001b3d;
        }

        .historia-section {
            padding: 35px;
        }

        .contenedor-historias {
            background: #ffffff;
            border: 1px solid #dce8f7;
            border-radius: 22px;
            padding: 30px;
            box-shadow: 0 12px 30px rgba(0, 27, 61, 0.08);
        }

        .encabezado-historias {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .encabezado-historias h2 {
            margin: 0;
            color: #001b3d;
            font-size: 28px;
        }

        .btn-nuevo {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            padding: 14px 22px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: bold;
            white-space: nowrap;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.25);
        }

        .btn-nuevo:hover {
            background: #1d4ed8;
        }

        .mensaje-exito {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 22px;
            font-weight: bold;
        }

        .form-busqueda {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .form-busqueda input {
            flex: 1;
            min-width: 0;
            padding: 15px 18px;
            border: 1px solid #cbd8e8;
            border-radius: 12px;
            font-size: 16px;
            outline: none;
            box-sizing: border-box;
        }

        .form-busqueda input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .btn-buscar,
        .btn-limpiar {
            padding: 15px 22px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-buscar {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-buscar:hover {
            background: #1d4ed8;
        }

        .btn-limpiar {
            background: #64748b;
            color: #ffffff;
        }

        .btn-limpiar:hover {
            background: #475569;
        }

        .resultado-busqueda {
            margin: -10px 0 22px;
            color: #475569;
        }

        .registro-clinico {
            background: #f8fafc;
            border: 1px solid #dbe5f1;
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .registro-clinico:last-child {
            margin-bottom: 0;
        }

        .registro-titulo {
            margin: 0 0 20px;
            color: #2563eb;
            font-size: 19px;
        }

        .fila-campos {
            display: grid;
            grid-template-columns: 1fr 1fr 1.4fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .campo {
            margin-bottom: 18px;
        }

        .campo:last-child {
            margin-bottom: 0;
        }

        .campo label {
            display: block;
            margin-bottom: 8px;
            color: #001b3d;
            font-weight: bold;
        }

        .campo input,
        .campo textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #cbd8e8;
            border-radius: 11px;
            background: #ffffff;
            color: #1e293b;
            font-size: 16px;
            box-sizing: border-box;
        }

        .campo textarea {
            min-height: 115px;
            resize: vertical;
            line-height: 1.5;
        }

        .sin-registros {
            padding: 35px 20px;
            border: 1px dashed #94a3b8;
            border-radius: 15px;
            text-align: center;
            color: #475569;
            background: #f8fafc;
            font-size: 17px;
        }

        @media (max-width: 950px) {
            .fila-campos {
                grid-template-columns: 1fr;
            }

            .encabezado-historias {
                align-items: flex-start;
                flex-direction: column;
            }

            .form-busqueda {
                align-items: stretch;
                flex-direction: column;
            }

            .btn-buscar,
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

            <a href="citas.php">
                📅 Citas
            </a>

            <a href="mascotas.php">
                🐶 Mascotas
            </a>

            <a
                class="active"
                href="historia_clinica.php"
            >
                📋 Historia Clínica
            </a>

            <a href="clientes.php">
                👥 Clientes
            </a>

            <a href="inventario.php">
                📦 Inventario
            </a>

            <a href="reportes.php">
                📄 Reportes
            </a>

            <a href="configuracion.php">
                ⚙️ Configuración
            </a>
        </nav>

        <a
            class="logout"
            href="logout.php"
        >
            ↩ Cerrar sesión
        </a>

    </aside>

    <main class="main-content">

        <header class="topbar">
            <h2>Historia Clínica</h2>
        </header>

        <section class="historia-section">

            <div class="contenedor-historias">

                <div class="encabezado-historias">

                    <h2>Registros Clínicos</h2>

                    <a
                        class="btn-nuevo"
                        href="nueva_historia.php"
                    >
                        + Nuevo Registro
                    </a>

                </div>

                <?php if (
                    isset($_GET["mensaje"]) &&
                    $_GET["mensaje"] === "registrado"
                ): ?>

                    <div class="mensaje-exito">
                        Historia clínica registrada correctamente.
                    </div>

                <?php endif; ?>

                <form
                    class="form-busqueda"
                    method="GET"
                    action="historia_clinica.php"
                >

                    <input
                        type="text"
                        name="buscar"
                        placeholder="Buscar mascota o paciente..."
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
                            href="historia_clinica.php"
                            class="btn-limpiar"
                        >
                            Limpiar
                        </a>

                    <?php endif; ?>

                </form>

                <?php if ($buscar !== ""): ?>

                    <p class="resultado-busqueda">
                        Resultados de búsqueda para:
                        <strong>
                            <?= htmlspecialchars($buscar) ?>
                        </strong>
                    </p>

                <?php endif; ?>

                <?php if ($resultado->num_rows > 0): ?>

                    <?php while (
                        $historia = $resultado->fetch_assoc()
                    ): ?>

                        <article class="registro-clinico">

                            <h3 class="registro-titulo">
                                Historia clínica #<?= (int) $historia["id"] ?>
                            </h3>

                            <div class="fila-campos">

                                <div class="campo">

                                    <label>
                                        Mascota o paciente
                                    </label>

                                    <input
                                        type="text"
                                        value="<?= htmlspecialchars(
                                            $historia["mascota"]
                                        ) ?>"
                                        readonly
                                    >

                                </div>

                                <div class="campo">

                                    <label>
                                        Fecha
                                    </label>

                                    <input
                                        type="text"
                                        value="<?= date(
                                            "d/m/Y",
                                            strtotime($historia["fecha"])
                                        ) ?>"
                                        readonly
                                    >

                                </div>

                                <div class="campo">

                                    <label>
                                        Veterinario
                                    </label>

                                    <input
                                        type="text"
                                        value="<?= htmlspecialchars(
                                            $historia["veterinario"]
                                        ) ?>"
                                        readonly
                                    >

                                </div>

                            </div>

                            <div class="campo">

                                <label>
                                    Diagnóstico
                                </label>

                                <textarea readonly><?= htmlspecialchars(
                                    $historia["diagnostico"]
                                ) ?></textarea>

                            </div>

                            <div class="campo">

                                <label>
                                    Tratamiento
                                </label>

                                <textarea readonly><?= htmlspecialchars(
                                    $historia["tratamiento"]
                                ) ?></textarea>

                            </div>

                        </article>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="sin-registros">

                        <?php if ($buscar !== ""): ?>

                            No se encontraron historias clínicas para
                            <strong>
                                <?= htmlspecialchars($buscar) ?>
                            </strong>.

                        <?php else: ?>

                            No existen historias clínicas registradas.

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

            </div>

        </section>

    </main>

</div>

</body>
</html>