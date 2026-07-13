<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../conexion.php';


if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Acceso no autorizado."]);
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

try {
    
    $query_caja = "SELECT id, monto_inicial FROM Apertura_caja WHERE is_usuario = :id_usuario AND estado = 'abierto' ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($query_caja);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $caja = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$caja) {
        echo json_encode(["status" => "warning", "message" => "No se encontró ninguna caja abierta para este usuario."]);
        exit();
    }

    $id_caja = $caja['id'];
    $monto_inicial = floatval($caja['monto_inicial']);

    $conn->beginTransaction();

    $query_ventas = "SELECT SUM(total) as total_ventas FROM Venta WHERE id_usuario = :id_usuario";
    $stmt_ventas = $conn->prepare($query_ventas);
    $stmt_ventas->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt_ventas->execute();
    $res_ventas = $stmt_ventas->fetch(PDO::FETCH_ASSOC);
    $total_ventas = $res_ventas['total_ventas'] ? floatval($res_ventas['total_ventas']) : 0.00;

    $monto_final = $monto_inicial + $total_ventas;
    
    $query_update = "UPDATE Apertura_caja SET estado = 'cerrado' WHERE id = :id_caja";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bindParam(':id_caja', $id_caja, PDO::PARAM_INT);
    $stmt_update->execute();

    $conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Cierre de caja procesado exitosamente.",
        "datos" => [
            "caja_id" => $id_caja,
            "monto_inicial" => $monto_inicial,
            "total_ventas" => $total_ventas,
            "monto_final_calculado" => $monto_final
        ]
    ]);

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error en el servidor: " . $e->getMessage()]);
}
?>