<?php
session_start();
require_once 'conexion.php'; 

// --- Lógica de Paginación ---
$limite = 6; // Productos por página
$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$inicio = ($pagina > 1) ? ($pagina * $limite) - $limite : 0;

// Contar total de productos para las páginas
$total_stmt = $conn->query("SELECT COUNT(*) FROM Producto");
$total_productos = $total_stmt->fetchColumn();
$total_paginas = ceil($total_productos / $limite);

// Consulta de productos con límite (QUITÉ 'descripcion' PARA EVITAR EL ERROR 1054)
$stmt = $conn->prepare("SELECT id, nombre, precio, stock, imagen FROM Producto LIMIT :inicio, :limite");
$stmt->bindParam(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Catálogo</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Mantenemos tu diseño original */
        header nav { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; }
        .btn-acceso-cliente { background-color: #f37023; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; }
        
        /* Ajuste para que el grid se vea como catálogo profesional */
        .grid-productos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .pagination { display: flex; list-style: none; justify-content: center; gap: 10px; margin-top: 30px; }
        .pagination a { padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px; }
        .pagination .active { background-color: #f37023; color: white; border-color: #f37023; }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="nav-info">
                <h2>ISW-306 · Proyecto Integrador</h2>
                <h3>Catálogo de Productos</h3>
            </div>
            <div class="nav-acceso">
                <a href="login_clientes.php" class="btn-acceso-cliente">
                    <i class="bi bi-person-bounding-box"></i> Acceso Clientes
                </a>
            </div>
        </nav>
    </header>

    <main>
        <section class="card" style="padding: 20px;">
            <h2>Productos Disponibles</h2>
            
            <div class="grid-productos">
                <?php
                if ($productos) {
                    foreach ($productos as $producto) {
                        $ruta_img = (!empty($producto['imagen'])) ? '../uploads/' . $producto['imagen'] : 'https://via.placeholder.com/300x200';
                        echo '<article class="producto-item" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background: #fff;">';
                        echo '<img src="'.$ruta_img.'" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px;">';
                        echo '<h3 style="color: #f37023; font-size: 1.3rem;">' . htmlspecialchars($producto['nombre']) . '</h3>';
                        echo '<p style="font-weight: bold;">$' . number_format($producto['precio'], 2) . ' DOP</p>';
                        echo '<p style="color: #666;">Stock: ' . $producto['stock'] . '</p>';
                        echo '</article>';
                    }
                } else {
                    echo "<p>No hay productos disponibles.</p>";
                }
                ?>
            </div>

            <!-- Paginación -->
            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="<?php echo ($pagina == $i) ? 'active' : ''; ?>">
                            <a href="?p=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </section>
    </main>
</body>
</html>