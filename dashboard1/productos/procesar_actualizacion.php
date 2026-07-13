<?php
session_start();
require_once '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    // Si se subió una imagen nueva
    if (!empty($_FILES['imagen']['name'])) {
        $nombre_archivo = time() . '_' . basename($_FILES['imagen']['name']);
        $ruta_destino = '../../uploads/' . $nombre_archivo;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            // Actualizar con nueva imagen
            $sql = "UPDATE Producto SET nombre=:nombre, precio=:precio, stock=:stock, imagen=:img WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':nombre'=>$nombre, ':precio'=>$precio, ':stock'=>$stock, ':img'=>$nombre_archivo, ':id'=>$id]);
        }
    } else {
        // Actualizar sin cambiar la imagen
        $sql = "UPDATE Producto SET nombre=:nombre, precio=:precio, stock=:stock WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':nombre'=>$nombre, ':precio'=>$precio, ':stock'=>$stock, ':id'=>$id]);
    }

    header("Location: lista.php?status=actualizado");
}
?>