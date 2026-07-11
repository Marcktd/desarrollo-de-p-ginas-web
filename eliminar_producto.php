<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once 'conexion.php';
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "message" => "Acceso denegado. Inicie sesión."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? $data['id'] : '';

if (empty($id)) {
    echo json_encode(["status" => "error", "message" => "ID de producto no proporcionado."]);
    exit();
}

try {
    $sql = "DELETE FROM Producto WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    echo json_encode(["status" => "success", "message" => "Producto eliminado correctamente."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Error al eliminar: " . $e->getMessage()]);
}
?>