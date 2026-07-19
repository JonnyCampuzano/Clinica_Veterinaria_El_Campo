<?php
session_start();
require_once __DIR__ . "/config/conexion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'] ?? 'Jonny';
$rol = $_SESSION['rol'] ?? 'Administrador';


   # FUNCIONES GENERALES

function tablaExiste($conexion, $tabla) {
    $tabla = $conexion->real_escape_string($tabla);
    $sql = $conexion->query("SHOW TABLES LIKE '$tabla'");
    return $sql && $sql->num_rows > 0;
}

function contarRegistros($conexion, $tabla) {
    if (!tablaExiste($conexion, $tabla)) {
        return 0;
    }

    $sql = $conexion->query("SELECT COUNT(*) AS total FROM $tabla");

    if ($sql) {
        $fila = $sql->fetch_assoc();
        return $fila['total'] ?? 0;
    }

    return 0;
}


   # ESTADÍSTICAS

$totalMascotas = contarRegistros($conexion, "mascotas");
$totalUsuarios = contarRegistros($conexion, "usuarios");
$totalHistorias = contarRegistros($conexion, "historia_clinica");

$totalCitasHoy = 0;

if (tablaExiste($conexion, "citas")) {
    $sqlCitas = $conexion->query("SELECT COUNT(*) AS total FROM citas WHERE DATE(fecha) = CURDATE()");
    if ($sqlCitas) {
        $filaCitas = $sqlCitas->fetch_assoc();
        $totalCitasHoy = $filaCitas['total'] ?? 0;
    }
}


  # NOTIFICACIONES

$totalNotificaciones = 0;
$notificaciones = [];

if (tablaExiste($conexion, "notificaciones")) {
    $sqlCount = $conexion->query("SELECT COUNT(*) AS total FROM notificaciones WHERE leido = 0");

    if ($sqlCount) {
        $filaCount = $sqlCount->fetch_assoc();
        $totalNotificaciones = $filaCount['total'] ?? 0;
    }

    $sqlNotif = $conexion->query("SELECT * FROM notificaciones ORDER BY fecha DESC LIMIT 5");

    if ($sqlNotif) {
        while ($fila = $sqlNotif->fetch_assoc()) {
            $notificaciones[] = $fila;
        }
    }
}

$inicial = strtoupper(substr($usuario, 0, 1));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Sistema Veterinario</title>

    <!-- Si tu CSS está en la carpeta principal, deja esta línea -->
    <link rel="stylesheet" href="dashboard.css?v=20">

    <!-- Si tu CSS está en assets/css, usa esta y borra la de arriba -->
    <!-- <link rel="stylesheet" href="assets/css/dashboard.css?v=20"> -->
</head>
<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div>
            <div class="brand">
                <div class="brand-logo">🐾</div>
                <div>
                    <h2>Sistema</h2>
                    <p>VETERINARIO</p>
                </div>
            </div>

            <nav class="menu">
                <a href="dashboard.php" class="menu-item active">📊 Dashboard</a>
                <a href="citas.php" class="menu-item">📅 Citas</a>
                <a href="mascotas.php" class="menu-item">🐶 Mascotas</a>
                <a href="historia_clinica.php" class="menu-item">📋 Historia Clínica</a>
                <a href="clientes.php" class="menu-item">👥 Clientes</a>
                <a href="inventario.php" class="menu-item">📦 Inventario</a>
                <a href="reportes.php" class="menu-item">📄 Reportes</a>
                <a href="configuracion.php" class="menu-item">⚙️ Configuración</a>
            </nav>
        </div>

        <a href="logout.php" class="logout-btn">↩ Cerrar Sesión</a>
    </aside>

    <!-- CONTENIDO -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <button class="menu-toggle" onclick="toggleSidebar()">☰</button>

            <form action="buscar.php" method="GET" class="search-box">
                <span class="search-icon">🔎</span>
                <input type="text" name="q" placeholder="Buscar pacientes, clientes, citas...">
            </form>

            <div class="top-actions">

                <!-- NOTIFICACIONES -->
                <div class="notification-wrapper">
                    <button type="button" class="notification-btn" onclick="toggleNotificaciones()">
                        🔔
                        <?php if ($totalNotificaciones > 0): ?>
                            <span class="notification-count">
                                <?php echo $totalNotificaciones; ?>
                            </span>
                        <?php endif; ?>
                    </button>

                    <div class="notification-menu" id="notificationMenu">
                        <div class="notification-header">
                            <strong>Notificaciones</strong>
                        </div>

                        <?php if (count($notificaciones) > 0): ?>
                            <?php foreach ($notificaciones as $noti): ?>
                                <div class="notification-item <?php echo ($noti['leido'] == 0) ? 'no-leida' : ''; ?>">
                                    <strong><?php echo htmlspecialchars($noti['titulo']); ?></strong>
                                    <p><?php echo htmlspecialchars($noti['mensaje']); ?></p>
                                    <small><?php echo htmlspecialchars($noti['fecha']); ?></small>
                                </div>
                            <?php endforeach; ?>

                            <form action="marcar_notificaciones.php" method="POST">
                                <button type="submit" class="btn-marcar">
                                    Marcar como leídas
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="notification-empty">
                                No hay notificaciones.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- USUARIO -->
                <div class="user-card">
                    <div class="avatar"><?php echo htmlspecialchars($inicial); ?></div>
                    <div class="user-text">
                        <strong><?php echo htmlspecialchars($usuario); ?></strong>
                        <span><?php echo htmlspecialchars(ucfirst($rol)); ?></span>
                    </div>
                </div>

            </div>
        </header>

        <!-- HERO -->
        <section class="hero-card">
            <div class="hero-content">
                

                <h1>Panel de Control</h1>

                <h3>
                    ¡Bienvenido, <?php echo htmlspecialchars($usuario); ?>! 👋
                </h3>

                <p>
                    Administra de manera rápida y organizada la información principal del sistema veterinario,
                    incluyendo pacientes, citas médicas, clientes, historias clínicas e inventario.
                </p>
            </div>

            <div class="hero-image">
                <img src="assets/img/doctor.png" alt="Doctor veterinario">
            </div>
        </section>

        <!-- TARJETAS -->
        <section class="stats-grid">

            <div class="stat-card blue">
                <div class="stat-icon">🐾</div>
                <div>
                    <p>Pacientes</p>
                    <h2><?php echo $totalMascotas; ?></h2>
                    <span>Registro total de mascotas</span>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon">📅</div>
                <div>
                    <p>Citas Hoy</p>
                    <h2><?php echo $totalCitasHoy; ?></h2>
                    <span>Citas programadas hoy</span>
                </div>
            </div>

            <div class="stat-card yellow">
                <div class="stat-icon">📁</div>
                <div>
                    <p>Historias Clínicas</p>
                    <h2><?php echo $totalHistorias; ?></h2>
                    <span>Registros clínicos</span>
                </div>
            </div>

            <div class="stat-card purple">
                <div class="stat-icon">👥</div>
                <div>
                    <p>Usuarios</p>
                    <h2><?php echo $totalUsuarios; ?></h2>
                    <span>Usuarios del sistema</span>
                </div>
            </div>

        </section>

        <!-- PANELES INFERIORES -->
        <section class="bottom-grid">

            <div class="panel">
                <h3>Acciones rápidas</h3>

                <div class="quick-actions">
                    <a href="nueva_cita.php" class="quick-btn">+ Nueva cita</a>
                    <a href="nueva_mascota.php" class="quick-btn">+ Nueva mascota</a>
                    <a href="nuevo_cliente.php" class="quick-btn">+ Nuevo cliente</a>
                    <a href="reportes.php" class="quick-btn">📄 Ver reportes</a>
                </div>
            </div>

            <div class="panel">
                <h3>Resumen rápido</h3>

                <ul class="summary-list">
                    <li>✅ Total de pacientes: <b><?php echo $totalMascotas; ?></b></li>
                    <li>✅ Citas para hoy: <b><?php echo $totalCitasHoy; ?></b></li>
                    <li>✅ Historias clínicas: <b><?php echo $totalHistorias; ?></b></li>
                    <li>✅ Usuarios registrados: <b><?php echo $totalUsuarios; ?></b></li>
                </ul>
            </div>

        </section>

    </main>
</div>

<script>
function toggleNotificaciones() {
    const menu = document.getElementById("notificationMenu");

    if (menu) {
        menu.classList.toggle("active");
    }
}

document.addEventListener("click", function(e) {
    const wrapper = document.querySelector(".notification-wrapper");
    const menu = document.getElementById("notificationMenu");

    if (wrapper && menu && !wrapper.contains(e.target)) {
        menu.classList.remove("active");
    }
});

function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");

    if (sidebar) {
        sidebar.classList.toggle("show");
    }
}
</script>
<script src="assets/js/app.js"></script>
</body>
</html>