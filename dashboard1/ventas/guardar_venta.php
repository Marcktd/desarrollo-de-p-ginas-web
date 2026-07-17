<?php
session_start();
require_once '../conexion.php'; 

// 1. Validar método y sesión
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Acceso no permitido']));
}

if (!isset($_SESSION['usuario_id'])) {
    die(json_encode(['success' => false, 'message' => 'Sesión expirada']));
}

$carrito = json_decode($_POST['carrito_datos'], true);
// Limpiamos el total para asegurar que sea un número decimal
$total = floatval(preg_replace('/[^0-9.]/', '', $_POST['total_venta']));
$usuario_id = $_SESSION['usuario_id'];

try {
    // 2. Iniciamos transacción
    $conn->beginTransaction();

    // 3. Insertar cabecera de la venta (usando el ID del usuario de la sesión)
    $sqlVenta = "INSERT INTO Venta (id_usuario, fecha, total, id_cliente) VALUES (:id_usuario, NOW(), :total, 1)";
    $stmtVenta = $conn->prepare($sqlVenta);
    $stmtVenta->execute([
        ':id_usuario' => $usuario_id,
        ':total' => $total
    ]);
    $venta_id = $conn->lastInsertId();

    // 4. Preparar sentencias para detalles y stock
    $sqlDetalle = "INSERT INTO Detalle_venta (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
    $sqlStock = "UPDATE Producto SET stock = stock - ? WHERE id = ?";
    $sqlCheck = "SELECT stock, nombre FROM Producto WHERE id = ?";

    $stmtDetalle = $conn->prepare($sqlDetalle);
    $stmtStock = $conn->prepare($sqlStock);
    $stmtCheck = $conn->prepare($sqlCheck);

    // 5. Procesar cada producto del carrito
    foreach ($carrito as $item) {
        // Validar stock antes de restar
        $stmtCheck->execute([$item['id']]);
        $prod = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$prod || $prod['stock'] < $item['cantidad']) {
            throw new Exception("Stock insuficiente para: " . ($prod['nombre'] ?? 'Producto desconocido'));
        }

        // Insertar detalle
        $stmtDetalle->execute([$venta_id, $item['id'], $item['cantidad'], $item['precio']]);
        
        // Actualizar stock
        $stmtStock->execute([$item['cantidad'], $item['id']]);
    }

    // 6. Confirmar transacción
    $conn->commit();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'id' => $venta_id]);
    exit();

} catch (Exception $e) {
    // Si algo falla, revertimos todos los cambios
    $conn->rollBack();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}