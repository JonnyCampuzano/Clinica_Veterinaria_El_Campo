<?php
session_start();

require_once __DIR__ . "/config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');
    $rol = trim($_POST['rol'] ?? '');

    if ($nombre == "" || $correo == "" || $contrasena == "" || $rol == "") {
        header("Location: registrar_usuario.php?error=campos_vacios");
        exit;
    }

    $roles_permitidos = ["administrador", "veterinario", "recepcionista"];

    if (!in_array($rol, $roles_permitidos)) {
        header("Location: registrar_usuario.php?error=rol_invalido");
        exit;
    }

    $verificar = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $verificar->bind_param("s", $correo);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        header("Location: registrar_usuario.php?error=correo_existe");
        exit;
    }

    $contrasena_segura = password_hash($contrasena, PASSWORD_DEFAULT);

    $sql = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol, estado) VALUES (?, ?, ?, ?, 'activo')");
    $sql->bind_param("ssss", $nombre, $correo, $contrasena_segura, $rol);

    if ($sql->execute()) {
        header("Location: registrar_usuario.php?success=registrado");
        exit;
    } else {
        header("Location: registrar_usuario.php?error=error_registro");
        exit;
    }

} else {
    header("Location: registrar_usuario.php");
    exit;
}
?>