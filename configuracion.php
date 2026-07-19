<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'] ?? 'Administrador';
$rol = $_SESSION['rol'] ?? 'administrador';
$inicial = strtoupper(substr($usuario, 0, 1));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
            <a href="clientes.php">👥 Clientes</a>
            <a href="inventario.php">📦 Inventario</a>
            <a href="reportes.php">📄 Reportes</a>
            <a class="active" href="configuracion.php">⚙️ Configuración</a>
        </nav>

        <a class="logout" href="logout.php">↩ Cerrar Sesión</a>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <h2>Configuración</h2>

            <div class="profile">
                <div class="avatar">
                    <?php echo htmlspecialchars($inicial); ?>
                </div>

                <div>
                    <strong>
                        <?php echo htmlspecialchars($usuario); ?>
                    </strong>
                    <small>
                        <?php echo htmlspecialchars(ucfirst($rol)); ?>
                    </small>
                </div>
            </div>
        </header>

        <section class="table-section">

            <h2>Datos del sistema</h2>

            <table>
                <tbody>
                    <tr>
                        <th>Nombre del sistema</th>
                        <td>Sistema Veterinario</td>
                    </tr>

                    <tr>
                        <th>Usuario actual</th>
                        <td><?php echo htmlspecialchars($usuario); ?></td>
                    </tr>

                    <tr>
                        <th>Rol</th>
                        <td><?php echo htmlspecialchars(ucfirst($rol)); ?></td>
                    </tr>

                    <tr>
                        <th>Estado</th>
                        <td>Activo</td>
                    </tr>
                </tbody>
            </table>

        </section>

    </main>

</div>

</body>
</html>