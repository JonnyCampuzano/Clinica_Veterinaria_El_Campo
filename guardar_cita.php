<?php
session_start();
require_once __DIR__ . "/config/conexion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fecha = trim($_POST['fecha'] ?? '');
    $hora = trim($_POST['hora'] ?? '');
    $paciente = trim($_POST['paciente'] ?? '');
    $propietario = trim($_POST['propietario'] ?? '');
    $motivo = trim($_POST['motivo'] ?? '');
    $estado = trim($_POST['estado'] ?? '');

    if (
        empty($fecha) ||
        empty($hora) ||
        empty($paciente) ||
        empty($propietario) ||
        empty($motivo) ||
        empty($estado)
    ) {
        header("Location: nueva_cita.php?error=campos_vacios");
        exit;
    }

    $sql = $conexion->prepare("
        INSERT INTO citas (fecha, hora, paciente, propietario, motivo, estado)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $sql->bind_param("ssssss", $fecha, $hora, $paciente, $propietario, $motivo, $estado);

    if ($sql->execute()) {

        /*
            Opcional:
            También crea una notificación cuando se registra una cita.
        */
        $titulo = "Nueva cita registrada";
        $mensaje = "Se registró una cita para " . $paciente . " con " . $propietario . ".";

        $verificarTabla = $conexion->query("SHOW TABLES LIKE 'notificaciones'");

        if ($verificarTabla && $verificarTabla->num_rows > 0) {
            $noti = $conexion->prepare("
                INSERT INTO notificaciones (titulo, mensaje, tipo, leido)
                VALUES (?, ?, 'cita', 0)
            ");
            $noti->bind_param("ss", $titulo, $mensaje);
            $noti->execute();
        }

        header("Location: citas.php");
        exit;

    } else {
        header("Location: nueva_cita.php?error=error_guardar");
        exit;
    }

} else {
    header("Location: nueva_cita.php");
    exit;
}
?>