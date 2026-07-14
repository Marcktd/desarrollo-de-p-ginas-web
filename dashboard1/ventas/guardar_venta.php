<?php
session_start();
require_once '../conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso no permitido");
}

$carrito = json_decode($_POST['carrito_datos'], true);
$total = $_POST['total_venta'];
$usuario_id = $_SESSION['usuario_id'];

try {
    // 1. Iniciamos transacción
    $conn->beginTransaction();

    // 2. Insertar cabecera de la venta
    // IMPORTANTE: 'id_usuario' está configurado como FK hacia 'Cliente', 
    // así que debemos enviar un ID de cliente válido (ej: 1).
    // Si tienes otra columna para el ID del vendedor, deberías usarla, 
    // pero por ahora, esto corregirá el error 1452.
    $sqlVenta = "INSERT INTO Venta (id_usuario, fecha, total, id_cliente) VALUES (1, NOW(), :total, 1)";
    $stmtVenta = $conn->prepare($sqlVenta);
    $stmtVenta->execute([':total' => $total]);
    $venta_id = $conn->lastInsertId();


    // 3. Insertar detalles y actualizar stock (Corregido a 'Detalle_venta')
   $sqlDetalle = "INSERT INTO Detalle_venta (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
$sqlStock = "UPDATE Producto SET stock = stock - ? WHERE id = ?"; // <--- ESTA LÍNEA TE FALTA

$stmtDetalle = $conn->prepare($sqlDetalle);
$stmtStock = $conn->prepare($sqlStock); // Ahora esto funcionará

foreach ($carrito as $item) {
    // Asegúrate de usar 'id' si así está en tu carrito, el resto está bien
    $stmtDetalle->execute([$venta_id, $item['id'], $item['cantidad'], $item['precio']]);
    $stmtStock->execute([$item['cantidad'], $item['id']]);
}

   // 4. Confirmar todo
    $conn->commit();

    // AQUÍ ES DONDE PUEDES VER LOS DATOS SI NECESITAS DEBUGGEAR
    // Pero recuerda que al usar header('Content-Type: application/json'), 
    // cualquier 'echo' extra corromperá tu respuesta JSON.
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'id' => $venta_id]);
    exit();

} catch (Exception $e) {
    $conn->rollBack();
    header('Content-Type: application/json');
    // Si la venta falla, este es el mensaje que verás en tu ventana emergente
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}