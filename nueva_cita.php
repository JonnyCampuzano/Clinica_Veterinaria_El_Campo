<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'] ?? 'Administrador';
$rol = $_SESSION['rol'] ?? 'administrador';
$inicial = strtoupper(substr($usuario, 0, 1));
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Cita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background: #f4f7fb;
            color: #10233f;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 245px;
            height: 100vh;
            background: linear-gradient(180deg, #0f2d56, #0b2245);
            color: white;
            padding: 22px 14px;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 34px;
            padding-left: 8px;
        }

        .logo-box {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: #2563eb;
            display: grid;
            place-items: center;
            font-size: 22px;
        }

        .sidebar-logo h2 {
            font-size: 16px;
            letter-spacing: 0.8px;
            color: #ffffff;
        }

        .sidebar-logo p {
            font-size: 12px;
            color: #bfdbfe;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar nav a,
        .logout {
            color: #dbeafe;
            text-decoration: none;
            padding: 13px 14px;
            border-radius: 12px;
            font-size: 15px;
            transition: 0.25s;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: #2563eb;
            color: #ffffff;
        }

        .logout {
            margin-top: auto;
        }

        .main-content {
            margin-left: 245px;
            width: calc(100% - 245px);
            min-height: 100vh;
            background: #f4f7fb;
            padding: 0 28px 32px;
        }

        .topbar {
            height: 76px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0 -28px 28px;
            padding: 0 32px;
        }

        .topbar h2 {
            font-size: 26px;
            color: #0f172a;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .avatar {
            width: 42px;
            height: 42px;
            background: #dbeafe;
            color: #1d4ed8;
            display: grid;
            place-items: center;
            border-radius: 50%;
            font-weight: 800;
        }

        .profile strong {
            display: block;
            font-size: 15px;
            color: #0f172a;
        }

        .profile small {
            display: block;
            color: #64748b;
            font-size: 12px;
        }

        .form-card {
            max-width: 760px;
            background: #ffffff;
            border-radius: 22px;
            padding: 34px;
            border: 1px solid #dbeafe;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-header h1 {
            font-size: 34px;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #64748b;
            font-size: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #eff6ff;
            border-radius: 16px;
            padding: 14px 16px;
            margin-bottom: 26px;
        }

        .user-info .avatar-big {
            width: 48px;
            height: 48px;
            background: #2563eb;
            color: white;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 18px;
        }

        .user-info strong {
            display: block;
            color: #0f172a;
            font-size: 16px;
        }

        .user-info span {
            color: #64748b;
            font-size: 13px;
        }

        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .formulario {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .campo {
            display: flex;
            flex-direction: column;
        }

        .campo label {
            margin-bottom: 8px;
            font-weight: 800;
            color: #1e293b;
            font-size: 14px;
        }

        .campo input,
        .campo select {
            width: 100%;
            height: 52px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 0 15px;
            font-size: 15px;
            outline: none;
            background: #ffffff;
            color: #0f172a;
        }

        .campo input:focus,
        .campo select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .acciones {
            display: flex;
            gap: 14px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .btn-guardar {
            background: #16a34a;
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 13px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            transition: 0.25s;
        }

        .btn-guardar:hover {
            background: #15803d;
            transform: translateY(-1px);
        }

        .btn-cancelar {
            background: #ef4444;
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 13px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: 0.25s;
        }

        .btn-cancelar:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        @media (max-width: 900px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .app {
                flex-direction: column;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 0 16px 24px;
            }

            .topbar {
                margin: 0 -16px 20px;
                padding: 0 16px;
            }

            .form-card {
                padding: 24px;
            }
        }

        @media (max-width: 600px) {
            .acciones {
                flex-direction: column;
            }

            .btn-guardar,
            .btn-cancelar {
                width: 100%;
                text-align: center;
            }

            .form-header h1 {
                font-size: 28px;
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
            <a class="active" href="citas.php">📅 Citas</a>
            <a href="mascotas.php">🐶 Mascotas</a>
            <a href="historia_clinica.php">📋 Historia Clínica</a>
            <a href="clientes.php">👥 Clientes</a>
            <a href="inventario.php">📦 Inventario</a>
            <a href="reportes.php">📄 Reportes</a>
            <a href="configuracion.php">⚙️ Configuración</a>
        </nav>

        <a class="logout" href="logout.php">↩ Cerrar Sesión</a>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <h2>Nueva Cita</h2>

            <div class="profile">
                <div class="avatar"><?php echo htmlspecialchars($inicial); ?></div>
                <div>
                    <strong><?php echo htmlspecialchars($usuario); ?></strong>
                    <small><?php echo htmlspecialchars(ucfirst($rol)); ?></small>
                </div>
            </div>
        </header>

        <section class="form-card">

            <div class="form-header">
                <h1>Registrar Nueva Cita</h1>
                <p>Complete los datos para registrar una nueva cita en el sistema veterinario.</p>
            </div>

            <div class="user-info">
                <div class="avatar-big"><?php echo htmlspecialchars($inicial); ?></div>
                <div>
                    <strong><?php echo htmlspecialchars($usuario); ?></strong>
                    <span><?php echo htmlspecialchars(ucfirst($rol)); ?></span>
                </div>
            </div>

            <?php if ($error == 'campos_vacios'): ?>
                <div class="alert-error">Complete todos los campos.</div>
            <?php elseif ($error == 'error_guardar'): ?>
                <div class="alert-error">No se pudo guardar la cita.</div>
            <?php endif; ?>

            <form action="guardar_cita.php" method="POST" class="formulario">

                <div class="campo">
                    <label for="fecha">Fecha</label>
                    <input type="date" id="fecha" name="fecha" required>
                </div>

                <div class="campo">
                    <label for="hora">Hora</label>
                    <input type="time" id="hora" name="hora" required>
                </div>

                <div class="campo">
                    <label for="paciente">Paciente / Mascota</label>
                    <input type="text" id="paciente" name="paciente" placeholder="Ej: Firulais" required>
                </div>

                <div class="campo">
                    <label for="propietario">Propietario</label>
                    <input type="text" id="propietario" name="propietario" placeholder="Ej: Juan Pérez" required>
                </div>

                <div class="campo">
                    <label for="motivo">Motivo</label>
                    <input type="text" id="motivo" name="motivo" placeholder="Ej: Consulta general" required>
                </div>

                <div class="campo">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Confirmada">Confirmada</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>

                <div class="acciones">
                    <button type="submit" class="btn-guardar">Guardar cita</button>
                    <a href="citas.php" class="btn-cancelar">Cancelar</a>
                </div>

            </form>

        </section>

    </main>

</div>

</body>
</html>