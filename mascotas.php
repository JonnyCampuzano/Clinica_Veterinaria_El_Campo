<?php
session_start();
require_once __DIR__ . "/config/conexion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}


if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);

    if ($id > 0) {
        $stmt = $conexion->prepare("DELETE FROM mascotas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: mascotas.php");
    exit;
}

$usuario = $_SESSION['usuario'] ?? 'Jonny';
$rol = $_SESSION['rol'] ?? 'Administrador';
$inicial = strtoupper(substr($usuario, 0, 1));

$mascotas = [];
$errorTabla = "";

try {
    $sql = $conexion->query("SELECT * FROM mascotas ORDER BY id DESC");

    if ($sql) {
        while ($fila = $sql->fetch_assoc()) {
            $mascotas[] = $fila;
        }
    }
} catch (mysqli_sql_exception $e) {
    $errorTabla = "No existe la tabla mascotas en la base de datos.";
}

$totalMascotas = count($mascotas);
$totalPerros = 0;
$totalGatos = 0;
$totalOtros = 0;

foreach ($mascotas as $mascota) {
    $especie = strtolower($mascota['especie'] ?? '');

    if ($especie == 'perro') {
        $totalPerros++;
    } elseif ($especie == 'gato') {
        $totalGatos++;
    } else {
        $totalOtros++;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mascotas</title>

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
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .card-header h2 {
            font-size: 29px;
            margin-bottom: 10px;
        }

        .card-header p {
            color: #60728d;
            font-size: 17px;
        }

        .btn-nuevo {
            background: #235be8;
            color: white;
            padding: 17px 27px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 800;
            box-shadow: 0 8px 18px rgba(35, 91, 232, 0.35);
        }

        .resumen {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
            margin-bottom: 30px;
        }

        .resumen-card {
            border-radius: 20px;
            padding: 28px;
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .resumen-card .icono {
            width: 66px;
            height: 66px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .resumen-card h3 {
            font-size: 42px;
            margin-bottom: 5px;
        }

        .resumen-card p {
            color: #536985;
            font-weight: 700;
        }

        .azul {
            background: #edf6ff;
            border: 1px solid #add2ff;
        }

        .azul .icono {
            background: #dcecff;
        }

        .naranja {
            background: #fff7ec;
            border: 1px solid #ffc983;
        }

        .naranja .icono {
            background: #fff0d8;
        }

        .verde {
            background: #ecfff5;
            border: 1px solid #98e7bd;
        }

        .verde .icono {
            background: #d9ffe9;
        }

        .rojo {
            background: #fff0f0;
            border: 1px solid #ffb6b6;
        }

        .rojo .icono {
            background: #ffe1e1;
        }

        .filtros {
            display: flex;
            gap: 18px;
            margin-bottom: 30px;
        }

        .filtros input {
            flex: 1;
            padding: 20px;
            border-radius: 16px;
            border: 1px solid #c5dcff;
            font-size: 17px;
            outline: none;
        }

        .filtros select {
            width: 280px;
            padding: 20px;
            border-radius: 16px;
            border: 1px solid #c5dcff;
            background: white;
            font-size: 16px;
            outline: none;
        }

        .tabla-box {
            border: 1px solid #d8e2ef;
            border-radius: 18px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f6f8fb;
        }

        th {
            text-align: left;
            padding: 20px;
            font-size: 17px;
            color: #10213d;
        }

        td {
            padding: 22px 20px;
            border-top: 1px solid #e1e8f2;
            font-size: 16px;
            color: #07152f;
        }

        .estado {
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 14px;
            display: inline-block;
        }

        .estado.activa {
            background: #e8fff3;
            color: #008a45;
        }

        .btn {
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 12px;
            font-weight: 800;
            margin-right: 7px;
            display: inline-block;
            font-size: 15px;
        }

        .editar {
            background: #dceaff;
            color: #1d5be3;
        }

        .eliminar {
            background: #ffe0e0;
            color: #e02020;
        }

        .sin-datos {
            text-align: center;
            color: #777;
            font-weight: 700;
        }

        .alerta {
            padding: 18px;
            background: #fff0f0;
            color: #d60000;
            border: 1px solid #ffb3b3;
            border-radius: 14px;
            font-weight: 700;
            margin-bottom: 25px;
        }

        @media (max-width: 1200px) {
            .resumen {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 850px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .resumen {
                grid-template-columns: 1fr;
            }

            .filtros {
                flex-direction: column;
            }

            .filtros select {
                width: 100%;
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
                <h1>Mascotas</h1>
                <p>Administra y controla las mascotas del sistema veterinario</p>
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
                    <h2>Gestión de Mascotas</h2>
                    <p>Consulta, filtra, edita y elimina las mascotas registradas.</p>
                </div>

                <a href="nueva_mascota.php" class="btn-nuevo">+ Nueva mascota</a>
            </div>

            <?php if ($errorTabla != ""): ?>
                <div class="alerta">
                    <?= htmlspecialchars($errorTabla) ?>
                </div>
            <?php endif; ?>

            <div class="resumen">

                <div class="resumen-card azul">
                    <div class="icono">🐾</div>
                    <div>
                        <h3><?= $totalMascotas ?></h3>
                        <p>Total de mascotas</p>
                    </div>
                </div>

                <div class="resumen-card naranja">
                    <div class="icono">🐶</div>
                    <div>
                        <h3><?= $totalPerros ?></h3>
                        <p>Perros</p>
                    </div>
                </div>

                <div class="resumen-card verde">
                    <div class="icono">🐱</div>
                    <div>
                        <h3><?= $totalGatos ?></h3>
                        <p>Gatos</p>
                    </div>
                </div>

                <div class="resumen-card rojo">
                    <div class="icono">🐰</div>
                    <div>
                        <h3><?= $totalOtros ?></h3>
                        <p>Otros</p>
                    </div>
                </div>

            </div>

            <div class="filtros">
                <input type="text" id="buscar" placeholder="🔎 Buscar por nombre, especie, raza o propietario...">

                <select id="filtroEspecie">
                    <option value="">Todas las especies</option>
                    <option value="perro">Perro</option>
                    <option value="gato">Gato</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <div class="tabla-box">
                <table id="tablaMascotas">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Especie</th>
                            <th>Raza</th>
                            <th>Edad</th>
                            <th>Propietario</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($mascotas) > 0): ?>
                            <?php foreach ($mascotas as $mascota): ?>
                                <tr>
                                    <td><?= htmlspecialchars($mascota['nombre'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($mascota['especie'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($mascota['raza'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($mascota['edad'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($mascota['propietario'] ?? '') ?></td>
                                    <td>
                                        <span class="estado activa">Activa</span>
                                    </td>
                                    <td>
    <a href="mascotas.php?eliminar=<?= $mascota['id'] ?>" 
   class="btn eliminar"
   onclick="return confirm('¿Seguro que deseas eliminar esta mascota?')">
   🗑 Eliminar
</a>
</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="sin-datos">
                                    No hay mascotas registradas
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </section>

    </main>

</div>

<script>
const buscar = document.getElementById("buscar");
const filtroEspecie = document.getElementById("filtroEspecie");
const filas = document.querySelectorAll("#tablaMascotas tbody tr");

function filtrarTabla() {
    let texto = buscar.value.toLowerCase();
    let especie = filtroEspecie.value.toLowerCase();

    filas.forEach(fila => {
        let contenido = fila.textContent.toLowerCase();
        let especieFila = fila.children[1]?.textContent.toLowerCase();

        let coincideTexto = contenido.includes(texto);
        let coincideEspecie = especie === "" || especieFila === especie;

        fila.style.display = coincideTexto && coincideEspecie ? "" : "none";
    });
}

buscar.addEventListener("keyup", filtrarTabla);
filtroEspecie.addEventListener("change", filtrarTabla);
</script>

</body>
</html>


