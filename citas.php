<?php
session_start();
require_once __DIR__ . "/config/conexion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'] ?? 'Administrador';
$rol = $_SESSION['rol'] ?? 'administrador';
$inicial = strtoupper(substr($usuario, 0, 1));

$citas = [];

$sql = $conexion->query("SELECT * FROM citas ORDER BY fecha ASC, hora ASC");

if ($sql) {
    while ($fila = $sql->fetch_assoc()) {
        $citas[] = $fila;
    }
}

$totalCitas = count($citas);
$totalPendientes = 0;
$totalConfirmadas = 0;
$totalCanceladas = 0;

foreach ($citas as $cita) {
    if ($cita['estado'] == 'Pendiente') {
        $totalPendientes++;
    } elseif ($cita['estado'] == 'Confirmada') {
        $totalConfirmadas++;
    } elseif ($cita['estado'] == 'Cancelada') {
        $totalCanceladas++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Citas</title>
    <link rel="stylesheet" href="assets/css/style.css?v=100">
</head>
<body>

<div class="app">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-box">🐾</div>
            <div>
                <h2>SISTEMA</h2>
                <p>VETERINARIO</p>
            </div>
        </div>

        <nav>
            <a href="dashboard.php">📊 Dashboard</a>
            <a class="active" href="citas.php">📅 Citas</a>
            <a href="mascotas.php">🐶 Mascotas</a>
            <a href="historia_clinica.php">📋 Historia Clínica</a>
            <a href="clientes.php">👥 Clientes</a>
            <a href="inventario.php">📦 Inventario</a>
            <a href="reportes.php">📄 Reportes</a>
            <a href="configuracion.php">⚙️ Configuración</a>
        </nav>

        <a class="logout" href="logout.php">↩ Cerrar Sesión</a>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-title">
                <h2>Citas</h2>
                <p>Administra y controla las citas del sistema veterinario</p>
            </div>

            <div class="profile">
                <div class="avatar"><?php echo htmlspecialchars($inicial); ?></div>
                <div>
                    <strong><?php echo htmlspecialchars($usuario); ?></strong>
                    <small><?php echo htmlspecialchars(ucfirst($rol)); ?></small>
                </div>
            </div>
        </header>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'actualizado'): ?>
    <div class="alert-success">
        Cita actualizada correctamente.
    </div>
<?php endif; ?>

<?php if (isset($_GET['success']) && $_GET['success'] == 'eliminado'): ?>
    <div class="alert-success">
        Cita eliminada correctamente.
    </div>
<?php endif; ?>

        <section class="table-section">

            <div class="table-header mejorado">
                <div>
                    <h2>Gestión de Citas</h2>
                    <p class="subtitle-section">Consulta, filtra, edita y elimina las citas registradas.</p>
                </div>

                <a class="btn-nueva-cita" href="nueva_cita.php">
                    + Nueva cita
                </a>
            </div>

            <!-- TARJETAS -->
            <div class="resumen-citas">
                <div class="card-cita total">
                    <div class="icono-card">📅</div>
                    <div>
                        <h3><?php echo $totalCitas; ?></h3>
                        <p>Total de citas</p>
                    </div>
                </div>

                <div class="card-cita pendiente">
                    <div class="icono-card">⏳</div>
                    <div>
                        <h3><?php echo $totalPendientes; ?></h3>
                        <p>Pendientes</p>
                    </div>
                </div>

                <div class="card-cita confirmada">
                    <div class="icono-card">✅</div>
                    <div>
                        <h3><?php echo $totalConfirmadas; ?></h3>
                        <p>Confirmadas</p>
                    </div>
                </div>

                <div class="card-cita cancelada">
                    <div class="icono-card">❌</div>
                    <div>
                        <h3><?php echo $totalCanceladas; ?></h3>
                        <p>Canceladas</p>
                    </div>
                </div>
            </div>

            <!-- FILTROS -->
            <div class="filtros-citas mejorados">
                <div class="input-buscar">
                    <span>🔎</span>
                    <input 
                        type="text" 
                        id="buscarCita" 
                        placeholder="Buscar por paciente, propietario o motivo..."
                        onkeyup="filtrarCitas()"
                    >
                </div>

                <select id="filtroEstado" onchange="filtrarCitas()">
                    <option value="todos">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>

            <!-- TABLA -->
            <div class="tabla-wrapper">
                <table id="tablaCitas">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Propietario</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($citas) > 0): ?>
                            <?php foreach ($citas as $cita): ?>
                                <?php
                                    $estadoMinuscula = strtolower($cita['estado']);

                                    if ($cita['estado'] == 'Pendiente') {
                                        $claseEstado = 'estado-pendiente';
                                    } elseif ($cita['estado'] == 'Confirmada') {
                                        $claseEstado = 'estado-confirmada';
                                    } else {
                                        $claseEstado = 'estado-cancelada';
                                    }
                                ?>

                                <tr data-estado="<?php echo htmlspecialchars($estadoMinuscula); ?>">
                                    <td><?php echo date("d/m/Y", strtotime($cita['fecha'])); ?></td>
                                    <td><?php echo date("h:i A", strtotime($cita['hora'])); ?></td>
                                    <td><?php echo htmlspecialchars($cita['paciente']); ?></td>
                                    <td><?php echo htmlspecialchars($cita['propietario']); ?></td>
                                    <td><?php echo htmlspecialchars($cita['motivo']); ?></td>
                                    <td>
                                        <span class="estado <?php echo $claseEstado; ?>">
                                            <?php echo htmlspecialchars($cita['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="acciones-tabla">
                                            <a class="btn-editar" href="editar_cita.php?id=<?php echo $cita['id']; ?>">
                                                ✏️ Actualizar
                                            </a>

                                            <form action="eliminar_cita.php" method="POST" class="form-inline">
                                                <input type="hidden" name="id" value="<?php echo $cita['id']; ?>">
                                                <button 
                                                    type="submit" 
                                                    class="btn-eliminar"
                                                    onclick="return confirm('¿Está seguro que desea eliminar esta cita?');"
                                                >
                                                    🗑 Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="sin-datos">No hay citas registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </section>

    </main>
</div>

<script>
function filtrarCitas() {
    let texto = document.getElementById("buscarCita").value.toLowerCase();
    let estado = document.getElementById("filtroEstado").value;
    let filas = document.querySelectorAll("#tablaCitas tbody tr");

    filas.forEach(function(fila) {
        let contenido = fila.textContent.toLowerCase();
        let estadoFila = fila.getAttribute("data-estado");

        let coincideTexto = contenido.includes(texto);
        let coincideEstado = estado === "todos" || estado === estadoFila;

        if (coincideTexto && coincideEstado) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }
    });
}
</script>

</body>
</html>