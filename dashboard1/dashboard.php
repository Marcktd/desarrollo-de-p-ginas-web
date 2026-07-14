<?php
session_start();

// --- ACTIVAR VISUALIZACIÓN DE ERRORES ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ----------------------------------------

require_once 'conexion.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Catálogo</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Estilos rápidos para acomodar el botón de login en el encabezado */
        header nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            flex-wrap: wrap;
        }
        .nav-info {
            display: flex;
            flex-direction: column;
        }
        .btn-acceso-cliente {
            background-color: #f37023; /* Naranja UAPA */
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-acceso-cliente:hover {
            background-color: #d65c18;
            transform: scale(1.02);
        }
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
        <section class="card">
            <h2>Productos Disponibles</h2>
            <p class="subtitulo">Selecciona el equipo para comprar.</p>
            
            <div class="grid-productos">
                <?php
                try {
                    // 1. Seleccionamos las columnas reales
                    $stmt = $conn->prepare("SELECT id, nombre, precio, stock, descripcion, imagen FROM Producto");
                    $stmt->execute();
                    
                    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($productos && count($productos) > 0) {
                        foreach ($productos as $producto) {
                            echo '<article class="producto-item" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background: #fff;">';
                            
                            // 2. ✅ CORRECCIÓN CLAVE: Agregamos '../' para salir de dashboard1 e ir a la raíz de uploads
                            if (!empty($producto['imagen'])) {
                                $ruta_img = '../uploads/' . $producto['imagen'];
                            } else {
                                $ruta_img = 'https://via.placeholder.com/300x200?text=Sin+Imagen';
                            }
                            
                            echo '<img src="' . htmlspecialchars($ruta_img) . '" alt="' . htmlspecialchars($producto['nombre']) . '" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px; display: block; margin: 0 auto;">';
                            
                            // 3. Datos del producto
                            echo '<h3 style="color: #f37023; margin-repeat: 0; margin-top: 15px; font-size: 1.3rem;">' . htmlspecialchars($producto['nombre']) . '</h3>';
                            echo '<p class="producto-precio" style="font-weight: bold; font-size: 1.1rem; margin: 5px 0;">$' . number_format($producto['precio'], 2) . ' DOP</p>';
                            
                            // 4. Stock disponible
                            echo '<p class="producto-desc" style="color: #666; font-size: 0.9rem;">Stock disponible: ' . htmlspecialchars($producto['stock']) . ' unidades</p>';
                            
                            echo '</article>';
                        }
                    } else {
                        echo "<p>No hay productos disponibles en este momento.</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p>Error al cargar productos: " . $e->getMessage() . "</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <p>&copy; 2026 Universidad Abierta para Adultos (UAPA) - Sistema POS</p>
    </footer>
    <script src="../app.js"></script>
</body>
</html>