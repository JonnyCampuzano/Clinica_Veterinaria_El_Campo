<?php
require_once __DIR__ . "/config/conexion.php";

$mensaje = "";
$linkReset = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST["correo"] ?? "");

    if ($correo == "") {
        $mensaje = "Ingrese su correo.";
    } else {
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ? LIMIT 1");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();

            $token = bin2hex(random_bytes(32));
            $expira = date("Y-m-d H:i:s", strtotime("+30 minutes"));

            $update = $conexion->prepare("UPDATE usuarios SET reset_token = ?, reset_expira = ? WHERE id = ?");
            $update->bind_param("ssi", $token, $expira, $usuario["id"]);
            $update->execute();

            $linkReset = "nueva_password.php?token=" . $token;
            $mensaje = "Se generó el enlace para cambiar la contraseña.";
        } else {
            $mensaje = "No existe un usuario con ese correo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>

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

        label {
            font-weight: bold;
            color: #1d2b44;
        }

        input {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
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
            background: #e8f1ff;
            color: #0f3b82;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 600;
        }

        .link-reset {
            display: block;
            background: #10b981;
            color: white;
            padding: 13px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            margin-top: 15px;
        }

        .volver {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #1557c0;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Recuperar contraseña</h2>

    <?php if ($mensaje != ""): ?>
        <div class="mensaje"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Correo</label>
        <input type="email" name="correo" placeholder="Ingrese su correo" required>

        <button type="submit">Generar enlace</button>
    </form>

    <?php if ($linkReset != ""): ?>
        <a class="link-reset" href="<?php echo $linkReset; ?>">
            Cambiar contraseña
        </a>
    <?php endif; ?>

    <a class="volver" href="login.php">Volver al login</a>
</div>

</body>
</html>