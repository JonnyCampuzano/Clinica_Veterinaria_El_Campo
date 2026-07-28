<?php
session_start();

if (!empty($_SESSION['usuario'])) {
    header('Location: dashboard.php');
    exit;
}

$error = $_GET['error'] ?? '';
$msg   = $_GET['msg'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistema Veterinario | Iniciar sesión</title>

    <link rel="stylesheet" href="assets/css/login.css">

    <style>
        .boton-registrar-login {
            display: block;
            margin-top: 14px;
            padding: 15px;
            background: #10b981;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
        }

        .boton-registrar-login:hover {
            background: #059669;
        }

        .alert-success {
            margin-bottom: 18px;
            padding: 14px;
            background: #dcfce7;
            color: #166534;
            border-radius: 10px;
            text-align: center;
        }
    </style>
</head>

<body>

<!-- Contenedor principal -->
<div class="login-wrapper">

    <section class="login-hero">
        <div class="brand">
            <div class="brand-icon">🐾</div>
            <h1>Sistema Veterinario</h1>
        </div>

        <div class="welcome">
            <h2>Bienvenido</h2>
            <p>Inicie sesión para acceder al sistema.</p>
        </div>

        <img
            src="assets/img/perro_gato.png"
            alt="Perro y gato"
            class="pets-img"
        >
    </section>

    <section class="login-panel">
        <form action="procesar_login.php" method="POST" class="login-card">

            <div class="lock-icon">🔒</div>
            <h2>Iniciar sesión</h2>

            <?php if ($msg === 'password_actualizada'): ?>
                <div class="alert-success">
                    Contraseña actualizada correctamente.
                </div>
            <?php endif; ?>

            <?php if ($error === 'campos_vacios'): ?>
                <div class="alert">Complete todos los campos.</div>

            <?php elseif ($error === 'credenciales_invalidas'): ?>
                <div class="alert">Correo o contraseña incorrectos.</div>

            <?php elseif ($error === 'usuario_inactivo'): ?>
                <div class="alert">El usuario está inactivo.</div>
            <?php endif; ?>

            <label for="correo">Correo</label>
            <input
                type="email"
                id="correo"
                name="correo"
                placeholder="Ingrese su correo"
                required
            >

            <label for="contrasena">Contraseña</label>
            <input
                type="password"
                id="contrasena"
                name="contrasena"
                placeholder="Ingrese su contraseña"
                required
            >

            <div class="options">
                <label class="remember">
                    <input type="checkbox" name="recordarme">
                    Recordarme
                </label>

                <a href="recuperar.php">
                    ¿Olvidó su contraseña?
                </a>
            </div>

            <button type="submit">Ingresar</button>

            <a href="registrar_usuario.php" class="boton-registrar-login">
                Registrar nuevo usuario
            </a>

            <p class="footer">
                © 2026 Sistema Veterinario
            </p>

        </form>
    </section>

</div>

<script src="assets/js/app.js"></script>

</body>
</html>