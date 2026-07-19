<?php
session_start();
require_once __DIR__ . "/config/conexion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$q = trim($_GET['q'] ?? '');
$resultados = [];

function tablaExiste($conexion, $tabla) {
    $tabla = $conexion->real_escape_string($tabla);
    $sql = $conexion->query("SHOW TABLES LIKE '$tabla'");
    return $sql && $sql->num_rows > 0;
}

function obtenerColumnas($conexion, $tabla) {
    $columnas = [];
    $tabla = $conexion->real_escape_string($tabla);
    $sql = $conexion->query("DESCRIBE $tabla");

    if ($sql) {
        while ($fila = $sql->fetch_assoc()) {
            $columnas[] = $fila['Field'];
        }
    }

    return $columnas;
}

function buscarEnTabla($conexion, $tabla, $titulo, $camposBuscar, $camposMostrar, $archivo, $q) {
    $datos = [];

    if (!tablaExiste($conexion, $tabla)) {
        return $datos;
    }

    $columnas = obtenerColumnas($conexion, $tabla);

    $camposValidosBuscar = array_values(array_intersect($camposBuscar, $columnas));
    $camposValidosMostrar = array_values(array_intersect($camposMostrar, $columnas));

    if (empty($camposValidosBuscar)) {
        return $datos;
    }

    if (empty($camposValidosMostrar)) {
        $camposValidosMostrar = $camposValidosBuscar;
    }

    $select = array_unique(array_merge(['id'], $camposValidosMostrar));
    $select = array_values(array_intersect($select, $columnas));

    $where = [];
    $params = [];

    foreach ($camposValidosBuscar as $campo) {
        $where[] = "$campo LIKE ?";
        $params[] = "%" . $q . "%";
    }

    $sql = "SELECT " . implode(", ", $select) . " FROM $tabla WHERE " . implode(" OR ", $where) . " LIMIT 10";
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return $datos;
    }

    $tipos = str_repeat("s", count($params));
    $stmt->bind_param($tipos, ...$params);
    $stmt->execute();

    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = [
            'tipo' => $titulo,
            'archivo' => $archivo,
            'datos' => $fila
        ];
    }

    return $datos;
}

if ($q !== '') {
    $resultados = array_merge(
        $resultados,
        buscarEnTabla(
            $conexion,
            "clientes",
            "Cliente",
            ["nombre", "apellido", "cedula", "correo", "telefono", "direccion"],
            ["nombre", "apellido", "cedula", "correo", "telefono"],
            "clientes.php",
            $q
        )
    );

    $resultados = array_merge(
        $resultados,
        buscarEnTabla(
            $conexion,
            "mascotas",
            "Mascota",
            ["nombre", "especie", "raza", "sexo", "color"],
            ["nombre", "especie", "raza", "sexo", "color"],
            "mascotas.php",
            $q
        )
    );

    $resultados = array_merge(
        $resultados,
        buscarEnTabla(
            $conexion,
            "citas",
            "Cita",
            ["fecha", "hora", "motivo", "estado", "cliente", "mascota"],
            ["fecha", "hora", "motivo", "estado", "cliente", "mascota"],
            "citas.php",
            $q
        )
    );
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de búsqueda</title>
    <link rel="stylesheet" href="dashboard.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f7fc;
            padding: 30px;
        }

        .contenedor {
            max-width: 950px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
        }

        h2 {
            color: #0f172a;
            margin-bottom: 20px;
        }

        .resultado {
            padding: 16px;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            margin-bottom: 14px;
            background: #ffffff;
        }

        .resultado strong {
            color: #2563eb;
            display: block;
            margin-bottom: 8px;
        }

        .resultado p {
            margin: 4px 0;
            color: #334155;
        }

        .btn-volver {
            display: inline-block;
            margin-bottom: 20px;
            background: #2563eb;
            color: white;
            padding: 12px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-ver {
            display: inline-block;
            margin-top: 10px;
            background: #10b981;
            color: white;
            padding: 9px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .sin-resultados {
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>

<div class="contenedor">
    <a href="dashboard.php" class="btn-volver">← Volver al dashboard</a>

    <h2>Resultados de búsqueda: "<?php echo htmlspecialchars($q); ?>"</h2>

    <?php if ($q == ''): ?>
        <div class="sin-resultados">Ingrese una palabra para buscar.</div>

    <?php elseif (count($resultados) > 0): ?>

        <?php foreach ($resultados as $item): ?>
            <div class="resultado">
                <strong><?php echo $item['tipo']; ?></strong>

                <?php foreach ($item['datos'] as $campo => $valor): ?>
                    <?php if ($campo != 'id'): ?>
                        <p>
                            <b><?php echo ucfirst($campo); ?>:</b>
                            <?php echo htmlspecialchars($valor); ?>
                        </p>
                    <?php endif; ?>
                <?php endforeach; ?>

                <a href="<?php echo $item['archivo']; ?>" class="btn-ver">
                    Ver módulo
                </a>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="sin-resultados">
            No se encontraron resultados para "<?php echo htmlspecialchars($q); ?>".
        </div>
    <?php endif; ?>
</div>

</body>
</html>