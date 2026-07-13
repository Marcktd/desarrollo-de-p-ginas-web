<?php
session_start();
require_once '../../conexion.php';

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Obtener productos
$stmt = $conn->query("SELECT * FROM Producto ORDER BY id DESC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Inventario de Productos</h2>
        <a href="agregar.php" class="btn btn-primary">+ Agregar Producto</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

        <div class="mb-3">
    <input type="text" id="busqueda" class="form-control" placeholder="Buscar producto por nombre y presiona Enter...">
</div>

<script>
document.getElementById('busqueda').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        let filtro = this.value.toLowerCase();
        let filas = document.querySelectorAll('table tbody tr');
        
        filas.forEach(fila => {
            let nombre = fila.cells[1].textContent.toLowerCase();
            fila.style.display = nombre.includes(filtro) ? '' : 'none';
        });
    }
});
</script>
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $row): ?>
                    <tr>
                        <td>
                            <img src="../../uploads/<?php echo $row['imagen']; ?>" width="60" class="rounded" alt="Producto">
                        </td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td>$<?php echo number_format($row['precio'], 2); ?></td>
                        <td><?php echo $row['stock']; ?></td>
                        <td>
                          <a href="actualizar_producto.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="eliminar_producto.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Seguro que deseas eliminar este producto?');">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>