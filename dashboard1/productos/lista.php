<?php
session_start();
require_once '../conexion.php';

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit();
}

// 1. Configuración de Búsqueda y Paginación
$productos_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_actual < 1) {
    $pagina_actual = 1;
}
$offset = ($pagina_actual - 1) * $productos_por_pagina;

// Obtener el término de búsqueda de la URL
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

try {
    if (!empty($busqueda)) {
        // Buscaremos por ID (quitando ceros a la izquierda), código de producto, código de barras o nombre
        $id_busqueda = ltrim($busqueda, '0');
        if (empty($id_busqueda)) {
            $id_busqueda = 0; // Evitar conflictos si solo escriben ceros
        }

        // Consulta para contar el total de resultados que coinciden con la búsqueda (incluye código de barras)
        $count_sql = "SELECT COUNT(*) FROM Producto 
                      WHERE id = :id 
                         OR codigo LIKE :query 
                         OR codigo_barras LIKE :query 
                         OR nombre LIKE :query";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bindValue(':id', $id_busqueda, PDO::PARAM_INT);
        $count_stmt->bindValue(':query', '%' . $busqueda . '%', PDO::PARAM_STR);
        $count_stmt->execute();
        $total_productos = $count_stmt->fetchColumn();

        // Consulta para traer los 10 productos de la página actual que coincidan
        $sql = "SELECT * FROM Producto 
                WHERE id = :id 
                   OR codigo LIKE :query 
                   OR codigo_barras LIKE :query 
                   OR nombre LIKE :query 
                ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id_busqueda, PDO::PARAM_INT);
        $stmt->bindValue(':query', '%' . $busqueda . '%', PDO::PARAM_STR);

    } else {
        // Si no hay búsqueda, se cuenta el total general de productos
        $total_productos = $conn->query("SELECT COUNT(*) FROM Producto")->fetchColumn();

        // Consulta estándar paginada
        $sql = "SELECT * FROM Producto ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($sql);
    }

    $total_paginas = ceil($total_productos / $productos_por_pagina);

    // Vinculamos limit y offset como enteros para que MySQL no proteste
    $stmt->bindValue(':limit', $productos_por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("🚨 Error al procesar los productos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos | Sistema de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<div class="container mt-5 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark"><i class="bi bi-box-seam me-2"></i>Inventario de Productos</h2>
        <div class="d-flex gap-2">
            <a href="../caja/procesar_apertura.php" class="btn btn-secondary d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <a href="agregar.php" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> Agregar Producto
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="lista.php" class="row g-2">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="busqueda" class="form-control border-start-0" 
                               placeholder="Buscar por ID, Código, Código de Barras o Nombre..." 
                               value="<?php echo htmlspecialchars($busqueda); ?>" autofocus>
                    </div>
                </div>
                <div class="col-md-3 d-grid gap-2 d-md-flex">
                    <button type="submit" class="btn btn-dark flex-grow-1">Buscar</button>
                    <?php if (!empty($busqueda)): ?>
                        <a href="lista.php" class="btn border btn-light" title="Limpiar Filtros">Limpiar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Código</th>
                            <th>Código de Barras</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($productos) > 0): ?>
                            <?php foreach ($productos as $row): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-primary">
                                    #<?php echo str_pad($row['id'], 6, '0', STR_PAD_LEFT); ?>
                                </td>
                                
                                <td class="text-secondary fw-semibold">
                                    <?php echo htmlspecialchars($row['codigo'] ?? '000000'); ?>
                                </td>

                                <td>
                                    <span class="font-monospace bg-light border px-2 py-1 rounded text-dark fs-7">
                                        <i class="bi bi-qr-code me-1"></i>
                                        <?php echo htmlspecialchars($row['codigo_barras'] ?? '000000000000'); ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <?php if (!empty($row['imagen'])): ?>
                                        <img src="../../uploads/<?php echo htmlspecialchars($row['imagen']); ?>" 
                                             alt="Producto" 
                                             class="rounded border" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">Sin imagen</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="fw-bold"><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td class="fw-bold text-dark">$<?php echo number_format($row['precio'], 2); ?></td>
                                <td>
                                    <span class="badge <?php echo $row['stock'] > 5 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> px-2 py-1">
                                        <?php echo $row['stock']; ?> u.
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="actualizar_producto.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-pencil-square"></i> Modificar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-emoji-neutral fs-2 d-block mb-2"></i>
                                    No se encontraron productos coincidentes con la búsqueda.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($total_paginas > 1): ?>
        <nav aria-label="Navegación de páginas">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $pagina_actual <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>">Anterior</a>
                </li>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php echo $pagina_actual == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php echo $pagina_actual >= $total_paginas ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<footer class="bg-dark text-white text-center py-4 mt-auto">
    <div class="container">
        <p class="mb-1">Universidad Abierta para Adultos (UAPA) &copy; 2026</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>