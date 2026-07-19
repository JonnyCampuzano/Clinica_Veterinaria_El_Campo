<?php
session_start();
require_once __DIR__ . "/config/conexion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'] ?? 'Jonny';
$rol = $_SESSION['rol'] ?? 'Administrador';
$inicial = strtoupper(substr($usuario, 0, 1));

$mensaje = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $especie = trim($_POST['especie'] ?? '');
    $raza = trim($_POST['raza'] ?? '');
    $edad = trim($_POST['edad'] ?? '');
    $propietario = trim($_POST['propietario'] ?? '');

    if ($nombre === "" || $especie === "" || $raza === "" || $edad === "" || $propietario === "") {
        $error = "Todos los campos son obligatorios.";
    } else {
        $stmt = $conexion->prepare("INSERT INTO mascotas (nombre, especie, raza, edad, propietario) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $especie, $raza, $edad, $propietario);

        if ($stmt->execute()) {
            header("Location: mascotas.php");
            exit;
        } else {
            $error = "Error al guardar la mascota.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Mascota</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        body {
            background: #f4f8fd;
            color: #06142e;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 290px;
            background: #0d315f;
            color: white;
            padding: 28px 18px;
            display: flex;
            flex-direction: column;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 45px;
        }

        .logo-icon {
            width: 56px;
            height: 56px;
            background: #2f6df6;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
        }

        .logo h2 {
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
        }

        .logo p {
            font-size: 14px;
            margin-top: 6px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 13px;
        }

        .menu a {
            color: white;
            text-decoration: none;
            padding: 16px 18px;
            border-radius: 14px;
            font-size: 17px;
            transition: 0.2s;
        }

        .menu a:hover,
        .menu a.activo {
            background: #2f6df6;
        }

        .cerrar {
            margin-top: auto;
            color: white;
            text-decoration: none;
            padding: 16px;
            font-size: 17px;
        }

        .contenido {
            flex: 1;
        }

        .header {
            height: 95px;
            background: white;
            border-bottom: 1px solid #dfe7f2;
            padding: 20px 38px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header h1 {
            font-size: 31px;
            margin-bottom: 8px;
        }

        .header p {
            color: #5d6f89;
            font-size: 16px;
        }

        .perfil {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .avatar {
            width: 52px;
            height: 52px;
            background: #dceafe;
            color: #1d5be3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
        }

        .perfil h3 {
            font-size: 17px;
        }

        .perfil p {
            font-size: 14px;
            color: #5d6f89;
        }

        .card {
            margin: 34px;
            background: white;
            border-radius: 24px;
            padding: 34px;
            border: 1px solid #d7e6fb;
            box-shadow: 0 16px 35px rgba(13, 49, 95, 0.08);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .card-header h2 {
            font-size: 29px;
            margin-bottom: 8px;
        }

        .card-header p {
            color: #60728d;
            font-size: 16px;
        }

        .btn-volver {
            background: #235be8;
            color: white;
            text-decoration: none;
            padding: 16px 26px;
            border-radius: 15px;
            font-weight: 800;
            box-shadow: 0 8px 18px rgba(35, 91, 232, 0.35);
        }

        .formulario {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 560px;
        }

        .grupo {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .grupo label {
            font-size: 17px;
            font-weight: 700;
            color: #06142e;
        }

        .grupo input,
        .grupo select {
            width: 100%;
            padding: 17px 18px;
            border-radius: 15px;
            border: 1px solid #c5dcff;
            font-size: 16px;
            outline: none;
            background: white;
        }

        .grupo input:focus,
        .grupo select:focus {
            border-color: #235be8;
            box-shadow: 0 0 0 3px rgba(35, 91, 232, 0.10);
        }

        .btn-guardar {
            background: #235be8;
            color: white;
            border: none;
            padding: 17px 28px;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            width: fit-content;
            box-shadow: 0 8px 18px rgba(35, 91, 232, 0.35);
            margin-top: 8px;
        }

        .btn-guardar:hover,
        .btn-volver:hover {
            opacity: 0.95;
        }

        .alerta-error {
            background: #fff0f0;
            color: #d60000;
            border: 1px solid #ffb3b3;
            padding: 15px;
            border-radius: 14px;
            font-weight: 700;
            margin-bottom: 22px;
            max-width: 560px;
        }

        @media (max-width: 850px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 18px;
            }

            .formulario {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <aside class="sidebar">
        <div class="logo">
            <div class="logo-icon">🐾</div>
            <div>
                <h2>SISTEMA</h2>
                <p>VETERINARIO</p>
            </div>
        </div>

        <nav class="menu">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="citas.php">🗓️ Citas</a>
            <a href="mascotas.php" class="activo">🐶 Mascotas</a>
            <a href="historia_clinica.php">📋 Historia Clínica</a>
            <a href="clientes.php">👥 Clientes</a>
            <a href="inventario.php">📦 Inventario</a>
            <a href="reportes.php">📄 Reportes</a>
            <a href="configuracion.php">⚙️ Configuración</a>
        </nav>

        <a href="logout.php" class="cerrar">↩ Cerrar Sesión</a>
    </aside>

    <main class="contenido">

        <header class="header">
            <div>
                <h1>Nueva Mascota</h1>
                <p>Registra una nueva mascota en el sistema veterinario</p>
            </div>

            <div class="perfil">
                <div class="avatar"><?= htmlspecialchars($inicial) ?></div>
                <div>
                    <h3><?= htmlspecialchars($usuario) ?></h3>
                    <p><?= htmlspecialchars($rol) ?></p>
                </div>
            </div>
        </header>

        <section class="card">

            <div class="card-header">
                <div>
                    <h2>Registrar Mascota</h2>
                    <p>Complete los datos de la mascota y su propietario.</p>
                </div>

                <a href="mascotas.php" class="btn-volver">← Volver</a>
            </div>

            <?php if ($error !== ""): ?>
                <div class="alerta-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="nueva_mascota.php" method="POST" class="formulario">

                <div class="grupo">
                    <label for="nombre">Nombre de la mascota</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Ej: Firulais" required>
                </div>

                <div class="grupo">
                    <label for="especie">Especie</label>
                    <select name="especie" id="especie" required>
                        <option value="">Seleccione una especie</option>
                        <option value="Perro">Perro</option>
                        <option value="Gato">Gato</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="grupo">
                    <label for="raza">Raza</label>
                    <input type="text" name="raza" id="raza" placeholder="Ej: Golden Retriever" required>
                </div>

                <div class="grupo">
                    <label for="edad">Edad</label>
                    <input type="text" name="edad" id="edad" placeholder="Ej: 3 años" required>
                </div>

                <div class="grupo">
                    <label for="propietario">Propietario</label>
                    <input type="text" name="propietario" id="propietario" placeholder="Nombre del propietario" required>
                </div>

                <button type="submit" class="btn-guardar">Guardar Mascota</button>

            </form>

        </section>

    </main>

</div>

</body>
</html>