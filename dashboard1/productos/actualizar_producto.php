<?php
session_start();
// Salimos dos niveles para buscar la conexión
require_once '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Obtenemos los datos del producto de forma segura con Prepared Statements
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // ✅ CORRECCIÓN: Usamos los nombres reales de las columnas en tu base de datos
    // id, nombre, precio, stock, codigo_barras, imagen
    $stmt = $conn->prepare("SELECT id, nombre, precio, stock, codigo_barras, imagen FROM Producto WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        die("Producto no encontrado. <a href='lista.php'>Volver</a>");
    }
} else {
    header("Location: lista.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto | Sistema de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<div class="container mt-5 flex-grow-1" style="max-width: 600px;">
    <div class="card shadow border-0">
        <div class="card-header bg-dark text-white py-3">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h5>
        </div>
        <div class="card-body p-4">
            <form action="procesar_actualizacion.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre del Producto</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Código de Barras</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-qr-code"></i></span>
                        <input type="text" name="codigo_barras" class="form-control" 
                               value="<?php echo isset($producto['codigo_barras']) ? htmlspecialchars($producto['codigo_barras']) : ''; ?>" 
                               placeholder="Escanea o escribe el código de barras">
                    </div>
                    <div class="form-text text-muted">Si lo dejas vacío, conservará el código de barras actual o se generará uno automático.</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Precio</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo $producto['precio']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Stock</label>
                        <input type="number" name="stock" class="form-control" value="<?php echo $producto['stock']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Imagen del Producto</label>
                    <input type="file" name="imagen" class="form-control" accept="image/*">
                    
                    <?php if (!empty($producto['imagen'])): ?>
                        <div class="mt-3 d-flex align-items-center gap-3 p-2 border rounded bg-white" style="max-width: 350px;">
                            <img src="../../uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Vista previa" class="img-thumbnail" style="width: 70px; height: 70px; object-fit: cover;">
                            <div>
                                <span class="d-block text-muted small">Imagen actual:</span>
                                <strong class="text-truncate d-inline-block" style="max-width: 200px;"><?php echo htmlspecialchars($producto['imagen']); ?></strong>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="form-text text-muted">Este producto no tiene una imagen asignada actualmente.</div>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Guardar Cambios</button>
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