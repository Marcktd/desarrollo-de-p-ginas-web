<?php
session_start();
// Nota: Cuando muevas conexion.php a la carpeta del dashboard, cambiarás esta ruta a '../conexion.php'
require_once '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    // 1. Recibimos el código de barras del formulario
    $codigo_barras = isset($_POST['codigo_barras']) ? trim($_POST['codigo_barras']) : '';

    // Si el usuario borró el código de barras, le generamos uno automático de 12 dígitos
    if (empty($codigo_barras)) {
        $codigo_barras = str_pad(rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
    }

    // Si se subió una imagen nueva
    if (!empty($_FILES['imagen']['name'])) {
        $nombre_archivo = time() . '_' . basename($_FILES['imagen']['name']);
        $ruta_destino = '../../uploads/' . $nombre_archivo;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            // 2. Actualizar con nueva imagen y código de barras
            $sql = "UPDATE Producto SET nombre=:nombre, precio=:precio, stock=:stock, codigo_barras=:codigo_barras, imagen=:img WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre, 
                ':precio' => $precio, 
                ':stock' => $stock, 
                ':codigo_barras' => $codigo_barras, 
                ':img' => $nombre_archivo, 
                ':id' => $id
            ]);
        }
    } else {
        // 3. Actualizar sin cambiar la imagen pero actualizando el código de barras
        $sql = "UPDATE Producto SET nombre=:nombre, precio=:precio, stock=:stock, codigo_barras=:codigo_barras WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre, 
            ':precio' => $precio, 
            ':stock' => $stock, 
            ':codigo_barras' => $codigo_barras, 
            ':id' => $id
        ]);
    }

    header("Location: lista.php?status=actualizado");
    exit();
}
?>