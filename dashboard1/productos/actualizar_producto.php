<?php
session_start();
require_once '../../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Obtenemos los datos del producto
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM Producto WHERE id = :id");
$stmt->execute([':id' => $id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<div class="container mt-5 flex-grow-1" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Editar Producto</div>
        <div class="card-body">
            <form action="procesar_actualizacion.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">

                <div class="mb-3">
                    <label class="form-label">Nombre del Producto</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo $producto['precio']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" value="<?php echo $producto['stock']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Imagen del Producto</label>
                    <input type="file" name="imagen" class="form-control" accept="image/*">
                    <small class="text-muted">Imagen actual: <?php echo $producto['imagen']; ?></small>
                </div>
                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                <a href="lista.php" class="btn btn-secondary">Cancelar</a>
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