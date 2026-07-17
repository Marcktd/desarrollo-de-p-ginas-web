<?php
/**
 * ====================================================================
 * CONTROLLER: API CAJA
 * Misión: Procesar peticiones asíncronas y flujos del estado de caja.
 * Respuestas: Formato estricto API (JSON).
 * Asignación: Etapa 3 - Servidor y Lógica Backend.
 * ====================================================================
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php'; 

header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$accion = $_GET['accion'] ?? '';

try {
    if ($accion === 'consultar') {
        $stmt = $conn->prepare("SELECT * FROM Apertura_caja WHERE estado = 'abierto' AND is_usuario = :id_usuario LIMIT 1");
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode($datos ? $datos : ['message' => 'No hay cajas abiertas']);

    } elseif ($accion === 'cerrar') {
        $stmt_caja = $conn->prepare("SELECT id, monto_inicial FROM Apertura_caja WHERE estado = 'abierto' AND is_usuario = :id_usuario LIMIT 1");
        $stmt_caja->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt_caja->execute();
        $caja = $stmt_caja->fetch(PDO::FETCH_ASSOC);

        if (!$caja) {
            echo json_encode(['success' => false, 'message' => 'No hay caja abierta para cerrar']);
            exit;
        }

        $id_caja = $caja['id'];
        $monto_inicial = floatval($caja['monto_inicial']);

        $conn->beginTransaction();

        $stmt_ventas = $conn->prepare("SELECT SUM(total) as total_ventas FROM Venta WHERE id_usuario = :id_usuario");
        $stmt_ventas->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt_ventas->execute();
        $res_ventas = $stmt_ventas->fetch(PDO::FETCH_ASSOC);
        $total_ventas = $res_ventas['total_ventas'] ? floatval($res_ventas['total_ventas']) : 0.00;

        $monto_final_calculado = $monto_inicial + $total_ventas;

        $stmt_update = $conn->prepare("UPDATE Apertura_caja SET estado = 'cerrado' WHERE id = :id_caja");
        $stmt_update->bindParam(':id_caja', $id_caja, PDO::PARAM_INT);
        $stmt_update->execute();

        $conn->commit();

        echo json_encode([
            'success' => true, 
            'message' => 'Caja cerrada correctamente',
            'resumen' => [
                'caja_id' => $id_caja,
                'monto_inicial' => $monto_inicial,
                'ventas_totales' => $total_ventas,
                'monto_final_sistema' => $monto_final_calculado
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>