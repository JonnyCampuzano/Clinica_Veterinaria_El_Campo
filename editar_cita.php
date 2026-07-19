<?php
session_start();
require_once __DIR__ . "/config/conexion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? '';

if (empty($id)) {
    header("Location: citas.php");
    exit;
}

$sql = $conexion->prepare("SELECT * FROM citas WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();

$resultado = $sql->get_result();

if ($resultado->num_rows == 0) {
    header("Location: citas.php");
    exit;
}

$cita = $resultado->fetch_assoc();

$usuario = $_SESSION['usuario'] ?? 'Administrador';
$rol = $_SESSION['rol'] ?? 'administrador';
$inicial = strtoupper(substr($usuario, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cita</title>
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

        .contenedor {
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
        }

        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid #dbeafe;
        }

        .encabezado {
            margin-bottom: 25px;
        }

        .encabezado h1 {
            font-size: 32px;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .encabezado p {
            color: #64748b;
            font-size: 15px;
        }

        .info-usuario {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding: 14px 16px;
            background: #eff6ff;
            border-radius: 14px;
        }

        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #2563eb;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }

        .info-usuario strong {
            display: block;
            color: #0f172a;
            font-size: 15px;
        }

        .info-usuario span {
            color: #64748b;
            font-size: 13px;
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
            font-weight: 700;
            color: #1e293b;
            font-size: 14px;
        }

        .campo input,
        .campo select {
            width: 100%;
            height: 48px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 0 14px;
            font-size: 14px;
            outline: none;
            background: #ffffff;
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

        .btn-actualizar {
            background: #16a34a;
            color: white;
            border: none;
            padding: 14px 22px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-actualizar:hover {
            background: #15803d;
        }

        .btn-volver {
            background: #ef4444;
            color: white;
            border: none;
            padding: 14px 22px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-volver:hover {
            background: #dc2626;
        }

        @media (max-width: 768px) {
            .contenedor {
                margin: 20px auto;
                padding: 14px;
            }

            .card {
                padding: 22px;
            }

            .encabezado h1 {
                font-size: 26px;
            }

            .acciones {
                flex-direction: column;
            }

            .btn-actualizar,
            .btn-volver {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="card">

        <div class="encabezado">
            <h1>Editar Cita</h1>
            <p>Modifique los datos de la cita y guarde los cambios.</p>
        </div>

        <div class="info-usuario">
            <div class="avatar"><?php echo htmlspecialchars($inicial); ?></div>
            <div>
                <strong><?php echo htmlspecialchars($usuario); ?></strong>
                <span><?php echo htmlspecialchars(ucfirst($rol)); ?></span>
            </div>
        </div>

        <form action="actualizar_cita.php" method="POST" class="formulario">
            <input type="hidden" name="id" value="<?php echo $cita['id']; ?>">

            <div class="campo">
                <label for="fecha">Fecha</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo htmlspecialchars($cita['fecha']); ?>" required>
            </div>

            <div class="campo">
                <label for="hora">Hora</label>
                <input type="time" id="hora" name="hora" value="<?php echo date('H:i', strtotime($cita['hora'])); ?>" required>
            </div>

            <div class="campo">
                <label for="paciente">Paciente</label>
                <input type="text" id="paciente" name="paciente" value="<?php echo htmlspecialchars($cita['paciente']); ?>" required>
            </div>

            <div class="campo">
                <label for="propietario">Propietario</label>
                <input type="text" id="propietario" name="propietario" value="<?php echo htmlspecialchars($cita['propietario']); ?>" required>
            </div>

            <div class="campo">
                <label for="motivo">Motivo</label>
                <input type="text" id="motivo" name="motivo" value="<?php echo htmlspecialchars($cita['motivo']); ?>" required>
            </div>

            <div class="campo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <option value="Pendiente" <?php echo ($cita['estado'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="Confirmada" <?php echo ($cita['estado'] == 'Confirmada') ? 'selected' : ''; ?>>Confirmada</option>
                    <option value="Cancelada" <?php echo ($cita['estado'] == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                </select>
            </div>

            <div class="acciones">
                <button type="submit" class="btn-actualizar">Actualizar cita</button>
                <a href="citas.php" class="btn-volver">Volver</a>
            </div>
        </form>

    </div>
</div>

</body>
</html>