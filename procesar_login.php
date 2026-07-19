<?php
session_start();
include "config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    if (empty($correo) || empty($contrasena)) {
        header("Location: login.php?error=campos_vacios");
        exit;
    }

    $sql = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $sql->bind_param("s", $correo);
    $sql->execute();

    $resultado = $sql->get_result();

    if ($resultado->num_rows == 1) {

        $usuario = $resultado->fetch_assoc();

        if ($usuario['estado'] == 'inactivo') {
            header("Location: login.php?error=usuario_inactivo");
            exit;
        }

        if (password_verify($contrasena, $usuario['contrasena'])) {

            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['usuario'] = $usuario['nombre'];
            $_SESSION['correo'] = $usuario['correo'];
            $_SESSION['rol'] = $usuario['rol'];

            header("Location: dashboard.php");
            exit;

        } else {
            header("Location: login.php?error=credenciales_invalidas");
            exit;
        }

    } else {
        header("Location: login.php?error=credenciales_invalidas");
        exit;
    }

} else {
    header("Location: login.php");
    exit;
}
?>