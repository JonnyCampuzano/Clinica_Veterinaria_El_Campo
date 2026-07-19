<?php
require_once __DIR__ . "/config/conexion.php";

$mensaje = "";
$token = $_GET["token"] ?? "";

if ($token == "") {
    die("Token no válido.");
}

$stmt = $conexion->prepare("SELECT id FROM usuarios WHERE reset_token = ? AND reset_expira > NOW() LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("El enlace expiró o no es válido.");
}

$usuario = $resultado->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $contrasena = $_POST["contrasena"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";

    if ($contrasena == "" || $confirmar == "") {
        $mensaje = "Complete todos los campos.";
    } elseif ($contrasena !== $confirmar) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        $contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

        $update = $conexion->prepare("
            UPDATE usuarios 
            SET contrasena = ?, reset_token = NULL, reset_expira = NULL 
            WHERE id = ?
        ");

        $update->bind_param("si", $contrasenaHash, $usuario["id"]);
        $update->execute();

        header("Location: login.php?msg=password_actualizada");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva contraseña</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef5ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: white;
            width: 420px;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }

        h2 {
            text-align: center;
            color: #0f3b82;
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            padding: 14px;
            margin-bottom: 18px;
            border-radius: 10px;
            border: 1px solid #b8c7dd;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #1557c0;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #0f3f8f;
        }

        .mensaje {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Nueva contraseña</h2>

    <?php if ($mensaje != ""): ?>
        <div class="mensaje"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="password" name="contrasena" placeholder="Nueva contraseña" required>
        <input type="password" name="confirmar" placeholder="Confirmar contraseña" required>

        <button type="submit">Guardar contraseña</button>
    </form>
</div>

</body>
</html>