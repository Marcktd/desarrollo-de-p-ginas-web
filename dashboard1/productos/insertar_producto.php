<?php
session_start();
// Salimos dos niveles para llegar a la raíz y buscar la conexión
require_once '../conexion.php'; 

// 1. Validar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Recibir y limpiar datos básicos
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $precio = isset($_POST['precio']) ? trim($_POST['precio']) : '';
    $stock = isset($_POST['stock']) ? trim($_POST['stock']) : '';
    $codigo_barras = isset($_POST['codigo_barras']) ? trim($_POST['codigo_barras']) : '';

    // 3. Validación previa de campos estrictamente obligatorios (Corregido el \vert{}\vert{})
    if (empty($nombre) || empty($precio) || empty($stock)) {
        die("Error: Todos los campos (Nombre, Precio y Stock) son obligatorios. <a href='agregar.php'>Volver</a>");
    }

    // Si el usuario dejó vacío el código de barras, generamos un código numérico aleatorio de 12 dígitos
    if (empty($codigo_barras)) {
        $codigo_barras = str_pad(rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
    }

    // Generamos el código interno automático (ej: PROD-7429)
    $codigo = 'PROD-' . rand(1000, 9999);
    
    // Asignamos una categoría por defecto
    $id_categoria = 2; 

    $nombre_archivo = null; // Por defecto es null (así la imagen es opcional)

    // 4. Manejo de la imagen (Solo si el usuario seleccionó una)
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $nombre_archivo = time() . '_' . basename($_FILES['imagen']['name']);
        $ruta_destino = '../../uploads/' . $nombre_archivo;

        // Asegurar que exista la carpeta uploads
        if (!is_dir('../../uploads/')) {
            mkdir('../../uploads/', 0777, true);
        }

        // Intentar mover el archivo a la carpeta uploads
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            die("Error al mover el archivo a la carpeta uploads. Verifica permisos.");
        }
    }

    // 5. Insertar en base de datos incluyendo 'codigo_barras'
    try {
        $sql = "INSERT INTO Producto (codigo, codigo_barras, nombre, precio, stock, id_categoria, imagen) 
                VALUES (:codigo, :codigo_barras, :nombre, :precio, :stock, :id_categoria, :imagen)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':codigo' => $codigo,
            ':codigo_barras' => $codigo_barras,
            ':nombre' => $nombre,
            ':precio' => $precio,
            ':stock' => $stock,
            ':id_categoria' => $id_categoria,
            ':imagen' => $nombre_archivo
        ]);

        // Éxito: redireccionar a la lista del inventario
        header("Location: lista.php?status=success");
        exit();

    } catch (PDOException $e) {
        die("🚨 Error en la Base de Datos: " . $e->getMessage());
    }
}
?>