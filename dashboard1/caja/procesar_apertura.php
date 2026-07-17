<?php
session_start();
require_once '../conexion.php'; // Conexión a la base de datos (un nivel arriba)

// 1. Validamos que el usuario haya iniciado sesión correctamente
if (!isset($_SESSION['usuario_id'])) {
    die("<b>ERROR:</b> No se detectó sesión de usuario. <a href='../login.php'>Volver al Login</a>");
}

$usuario_id = $_SESSION['usuario_id'];

// 2. Verificar si ya existe una caja abierta para este usuario
$stmtCheck = $conn->prepare("SELECT id FROM Caja WHERE id_usuario = ? AND estado = 'abierta' ORDER BY id DESC LIMIT 1");
$stmtCheck->execute([$usuario_id]);
$caja_existente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

// 3. Si no hay caja abierta y el usuario envió el monto inicial, la abrimos en la base de datos
if (!$caja_existente && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['monto_inicial'])) {
    $monto_inicial = floatval($_POST['monto_inicial']);
    
    try {
        $stmtInsert = $conn->prepare("INSERT INTO Caja (id_usuario, monto_inicial, estado, fecha_apertura) VALUES (?, ?, 'abierta', NOW())");
        $stmtInsert->execute([$usuario_id, $monto_inicial]);
        
        // Refrescamos la validación para confirmar que ya está abierta
        $caja_existente = true; 
    } catch (PDOException $e) {
        die("<b>ERROR al abrir caja en la base de datos:</b> " . $e->getMessage());
    }
} 
// Si no hay caja abierta en la Base de Datos y tampoco se envió un monto inicial por formulario
elseif (!$caja_existente) {
    // Redireccionamos a la pantalla de abrir caja para que ingrese el monto inicial
    header("Location: ../abrir_caja.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Caja Abierta | Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .card { border: none; }
    </style>
</head>
<body class="d-flex flex-column vh-100">

<div class="container mt-5 flex-grow-1" style="max-width: 600px;">
    
    <!-- Mensaje de confirmación dinámico -->
    <div class="alert alert-success text-center shadow-sm py-4">
        <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
        <h4 class="alert-heading fw-bold">¡Caja abierta correctamente!</h4>
        <p class="mb-0">El sistema ha registrado el inicio de tu turno en la base de datos y está listo para operar.</p>
    </div>

    <!-- Tarjeta de Diagnóstico -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white fw-bold d-flex align-items-center">
            <i class="bi bi-shield-check me-2"></i> Diagnóstico de Sesión Activa
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>ID de sesión:</strong> <code class="text-secondary"><?php echo session_id(); ?></code></p>
            <p class="mb-2"><strong>ID de usuario (Vendedor):</strong> <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($usuario_id); ?></span></p>
            <p class="mb-0"><strong>Correo electrónico:</strong> <span class="text-muted"><?php echo htmlspecialchars($_SESSION['usuario_correo'] ?? 'No definido'); ?></span></p>
        </div>
    </div>

    <!-- Menú de Acciones Rápidas -->
    <div class="d-grid gap-3">
        <a href="../ventas/realizar_venta.php" class="btn btn-primary btn-lg py-3 shadow-sm fw-bold">
            <i class="bi bi-cart-plus-fill me-2"></i> Realizar una Venta
        </a>
        <a href="../productos/lista.php" class="btn btn-info btn-lg py-3 text-white shadow-sm fw-bold">
            <i class="bi bi-box-seam-fill me-2"></i> Agregar/Gestionar Productos
        </a>
        <a href="../reportecaja/reportes.php" class="btn bg-secondary text-white btn-lg py-3 shadow-sm fw-bold">
            <i class="bi bi-file-earmark-bar-graph-fill me-2"></i> Ver Reporte de Ventas
        </a>
        
        <hr class="my-4">
        
        <!-- Botones de Salida / Control -->
        <div class="row g-2">
            <div class="col-6">
                <!-- Enlace corregido a cerrar_caja.php (están en la misma carpeta 'caja/') -->
                <a href="cerra_caja.php" class="btn btn-danger w-100 py-2 fw-bold">
                    <i class="bi bi-calculator-fill me-1"></i> Cerrar Caja (Corte)
                </a>
            </div>
            <div class="col-6">
                <!-- Enlace para salir de la sesión del usuario (un nivel arriba) -->
                <a href="../logout.php" class="btn btn-outline-secondary w-100 py-2 fw-bold">
                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-4 mt-5">
    <div class="container">
        <p class="mb-1">Universidad Abierta para Adultos (UAPA) &copy; 2026</p>
        <small class="text-white-50">Sistema de Ventas - Módulo de Caja</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>