<?php
session_start();
// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto | Sistema de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<div class="container mt-5 flex-grow-1" style="max-width: 600px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white py-3">
            <h5 class="mb-0">Agregar Nuevo Producto</h5>
        </div>
        <div class="card-body p-4">
            <form action="insertar_producto.php" method="POST" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre del Producto</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Computadora" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" placeholder="0.00" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Stock</label>
                    <input type="number" name="stock" class="form-control" placeholder="0" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Imagen del Producto</label>
                    <input type="file" name="imagen" class="form-control" accept="image/*" >
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Guardar Producto</button>
                    <a href="lista.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-4 mt-auto">
    <div class="container">
        <p class="mb-1">Universidad Abierta para Adultos (UAPA) &copy; 2026</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>