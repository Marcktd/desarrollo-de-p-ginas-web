<?php
session_start();
// Salimos dos niveles para llegar a la raíz y buscar la conexión
require_once '../../conexion.php'; 

// 1. Validar que el usuario sea administrador
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Recibir datos básicos
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    // 3. Manejo de la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $nombre_archivo = time() . '_' . basename($_FILES['imagen']['name']);
        $ruta_destino = '../../uploads/' . $nombre_archivo;

        // Intentar mover el archivo a la carpeta uploads
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            
            // 4. Insertar en base de datos
            $sql = "INSERT INTO Producto (nombre, precio, stock, imagen) VALUES (:nombre, :precio, :stock, :imagen)";
            $stmt = $conn->prepare($sql);
            
            $stmt->execute([
                ':nombre' => $nombre,
                ':precio' => $precio,
                ':stock' => $stock,
                ':imagen' => $nombre_archivo // Guardamos el nombre del archivo
            ]);

            // Éxito: redireccionar al panel
            header("Location: ../dashboard.php?status=success");
            exit();
        } else {
            echo "Error al mover el archivo a la carpeta uploads. Verifica permisos.";
        }
        
        if (empty($nombre) || empty($precio) || empty($stock)) {
    die("Error: Todos los campos son obligatorios. <a href='agregar.php'>Volver</a>");
}
    } else {
        echo "Error al subir la imagen. Verifica que el archivo sea válido.";
    }
}
?>