<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 1. Definir respuesta estricta en JSON
header('Content-Type: application/json; charset=UTF-8');
require_once 'conexion.php';

// 2. Validar sesión unificada
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Acceso denegado."]);
    exit();
}

// Determinar si la petición es para CREAR (POST) o para LEER (GET)
$metodo = $_SERVER['REQUEST_METHOD'];

try {
    if ($metodo === 'GET') {
        // --- OPERACIÓN: READ (LEER) ---
        $stmt = $conn->prepare("SELECT id, nombre, precio, stock FROM Producto");
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($productos);
        
    } elseif ($metodo === 'POST') {
        // --- OPERACIÓN: CREATE (CREAR) ---
        $data = json_decode(file_get_contents("php://input"), true);
        $nombre = $data['nombre'] ?? '';
        $precio = $data['precio'] ?? '';
        $stock = $data['stock'] ?? '';

        if (empty($nombre) || empty($precio) || empty($stock)) {
            echo json_encode(["status" => "error", "message" => "Faltan campos obligatorios."]);
            exit();
        }

        $sql = "INSERT INTO Producto (nombre, precio, stock) VALUES (:nombre, :precio, :stock)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':stock', $stock);
        $stmt->execute();

        echo json_encode(["status" => "success", "message" => "Producto creado correctamente."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error en el servidor: " . $e->getMessage()]);
}
?>