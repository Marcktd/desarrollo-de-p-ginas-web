<?php
session_start();

// Validamos que el usuario haya iniciado sesión correctamente
if (!isset($_SESSION['usuario_id'])) {
    die("<b>ERROR:</b> No se detectó sesión de usuario. <a href='login.php'>Volver al Login</a>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Caja Abierta | Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">
    <div class="alert alert-success text-center">
        <h4 class="alert-heading">¡Caja abierta correctamente!</h4>
        <p>El sistema está listo para operar.</p>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">Diagnóstico de Sesión</div>
        <div class="card-body">
            <p><strong>ID de sesión:</strong> <?php echo session_id(); ?></p>
            <p><strong>ID de usuario:</strong> <?php echo $_SESSION['usuario_id']; ?></p>
            <p><strong>Correo en sesión:</strong> <?php echo $_SESSION['usuario_correo']; ?></p>
        </div>
    </div>

    <div class="d-grid gap-2">
       <a href="../ventas/realizar_venta.php" class="btn btn-primary">Realizar una Venta a</a>
      <a href="../productos/lista.php" class="btn btn-info btn-lg text-white">Agregar/Gestionar Productos</a>
        <a href="reportes.php" class="btn btn-secondary btn-lg">Ver Reporte Parcial</a>
        <hr>
        <a href="logout.php" class="btn btn-outline-danger">Cerrar Sesión</a>
    </div>
</div>
  <hr>
 <footer class="bg-dark text-white text-center py-4 mt-auto">
        <div class="container">
            <p class="mb-1">Universidad Abierta para Adultos (UAPA) &copy; 2026</p>
            <small class="text-white-50">Sistema de Ventas - Módulo de login</small>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>