<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit;
}

$error = $_GET['error'] ?? '';
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Veterinario - Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/login.css">

    <style>
        .boton-registrar-login {
            display: block !important;
            width: 100% !important;
            height: 52px !important;
            line-height: 52px !important;
            margin-top: 14px !important;
            text-align: center !important;
            background: #10b981 !important;
            color: #ffffff !important;
            border-radius: 10px !important;
            text-decoration: none !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            box-sizing: border-box !important;
        }

        .boton-registrar-login:hover {
            background: #059669 !important;
            color: #ffffff !important;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 14px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 18px;
            font-weight: 600;
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
            <h2>Bienvenido</h2>
            <p>Inicia sesión para continuar accediendo al sistema.</p>
        </div>

        <img class="pets-img" src="assets/img/perro_gato.png" alt="Perro y gato">
    </section>

    <section class="login-panel">
        <form action="procesar_login.php" method="POST" class="login-card">
            <div class="lock-icon">🔒</div>
            <h2>Iniciar Sesión</h2>

            <?php if ($msg == 'password_actualizada'): ?>
                <div class="alert-success">Contraseña actualizada correctamente. Ya puede iniciar sesión.</div>
            <?php endif; ?>

            <?php if ($error == 'campos_vacios'): ?>
                <div class="alert">Complete todos los campos.</div>
            <?php elseif ($error == 'credenciales_invalidas'): ?>
                <div class="alert">Correo o contraseña incorrectos.</div>
            <?php elseif ($error == 'usuario_inactivo'): ?>
                <div class="alert">El usuario está inactivo.</div>
            <?php endif; ?>

            <label>Usuario</label>
            <input type="email" name="correo" placeholder="Ingrese su correo" required>

            <label>Contraseña</label>
            <input type="password" name="contrasena" placeholder="Ingrese su contraseña" required>

            <div class="options">
                <label class="remember">
                    <input type="checkbox" name="recordarme"> Recordarme
                </label>

                <a href="recuperar.php">¿Olvidó su contraseña?</a>
            </div>

            <button type="submit">Ingresar</button>

            <a href="registrar_usuario.php" class="boton-registrar-login">
                Registrar nuevo usuario
            </a>

            <p class="footer">© 2026 Sistema Veterinario. Todos los derechos reservados.</p>
        </form>
    </section>

</div>

<script src="assets/js/app.js"></script>

</body>
</html>