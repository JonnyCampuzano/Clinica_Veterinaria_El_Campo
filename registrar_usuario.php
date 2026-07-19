<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit;
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Veterinario - Registrar Usuario</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <style>
    .login-card input[name="nombre"],
    .login-card input[name="correo"],
    .login-card input[name="contrasena"],
    .login-card select[name="rol"] {
        width: 100% !important;
        height: 52px !important;
        padding: 0 16px !important;
        margin-bottom: 22px !important;
        border: 1px solid #c7d3e5 !important;
        border-radius: 10px !important;
        font-size: 15px !important;
        outline: none !important;
        background: #ffffff !important;
        color: #1f2937 !important;
        box-sizing: border-box !important;
    }

    .login-card select[name="rol"] {
        appearance: auto !important;
        cursor: pointer !important;
    }

    .login-card button {
        width: 100% !important;
        height: 52px !important;
        border: none !important;
        border-radius: 10px !important;
        background: #2563eb !important;
        color: white !important;
        font-size: 16px !important;
        font-weight: bold !important;
        cursor: pointer !important;
        margin-top: 4px !important;
    }

    .login-card button:hover {
        background: #1d4ed8 !important;
    }

    .login-card .btn-volver,
    .login-card a[href="login.php"] {
        display: block !important;
        width: 100% !important;
        height: 52px !important;
        line-height: 52px !important;
        margin-top: 14px !important;
        text-align: center !important;
        background: #10b981 !important;
        color: white !important;
        border-radius: 10px !important;
        text-decoration: none !important;
        font-size: 16px !important;
        font-weight: bold !important;
        box-sizing: border-box !important;
    }

    .login-card .btn-volver:hover,
    .login-card a[href="login.php"]:hover {
        background: #059669 !important;
        color: white !important;
    }
</style>
</head>
<body>

<div class="login-wrapper">

    <section class="login-hero">
        <div class="brand">
            <div class="brand-icon">🐾</div>
            <h1>Sistema Veterinario</h1>
        </div>

        <div class="welcome">
            <h2>Registro de Usuario</h2>
            <p>Registra al administrador, veterinario o recepcionista del sistema.</p>
        </div>

        <img class="pets-img" src="assets/img/perro_gato.png" alt="Perro y gato">
    </section>

    <section class="login-panel">
        <form action="procesar_registro.php" method="POST" class="login-card">
            <div class="lock-icon">👤</div>
            <h2>Registrar Usuario</h2>

            <?php if ($error == 'campos_vacios'): ?>
                <div class="alert">Complete todos los campos.</div>
            <?php elseif ($error == 'correo_existe'): ?>
                <div class="alert">Este correo ya está registrado.</div>
            <?php elseif ($error == 'rol_invalido'): ?>
                <div class="alert">Seleccione un rol válido.</div>
            <?php elseif ($error == 'error_registro'): ?>
                <div class="alert">No se pudo registrar el usuario.</div>
            <?php endif; ?>

            <?php if ($success == 'registrado'): ?>
                <div class="alert success">Usuario registrado correctamente.</div>
            <?php endif; ?>

            <label>Nombre completo</label>
            <input type="text" name="nombre" placeholder="Ingrese el nombre completo" required>

            <label>Correo</label>
            <input type="email" name="correo" placeholder="Ingrese el correo" required>

            <label>Contraseña</label>
            <input type="password" name="contrasena" placeholder="Ingrese la contraseña" required>

            <label>Rol</label>
            <select name="rol" required>
                <option value="">Seleccione un rol</option>
                <option value="administrador">Administrador</option>
                <option value="veterinario">Veterinario</option>
                <option value="recepcionista">Recepcionista</option>
            </select>

            <button type="submit">Registrar</button>

            <a href="login.php" class="btn-volver">
              Volver al login
            </a>

            <p class="footer">© 2026 Sistema Veterinario. Todos los derechos reservados.</p>
        </form>
    </section>

</div>

</body>
</html>