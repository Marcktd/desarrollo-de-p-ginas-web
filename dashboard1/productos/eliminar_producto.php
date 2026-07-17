<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Primero buscamos el nombre de la imagen para borrarla del servidor
    $stmt = $conn->prepare("SELECT imagen FROM Producto WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        $ruta_imagen = '../../uploads/' . $producto['imagen'];
        // Borramos el archivo físico si existe
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }

        // 2. Eliminamos el registro de la base de datos
        $stmt_del = $conn->prepare("DELETE FROM Producto WHERE id = :id");
        $stmt_del->execute([':id' => $id]);
    }
}

header("Location: lista.php?status=eliminado");
exit();
?>