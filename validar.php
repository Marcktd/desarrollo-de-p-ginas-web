<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php'; 

$data = json_decode(file_get_contents("php://input"), true);
$correo = isset($data['correo']) ? trim($data['correo']) : (isset($_POST['correo']) ? trim($_POST['correo']) : '');
$password = isset($data['password']) ? trim($data['password']) : (isset($_POST['password']) ? trim($_POST['password']) : '');

if (empty($correo) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Credenciales incompletas."]);
    exit();
}

try {
    $sql = "SELECT id, correo FROM Usuario WHERE correo = :correo AND password = :password LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_correo'] = $user['correo']; 
        
        echo json_encode([
            "status" => "success",
            "message" => "Sesión iniciada correctamente.",
            "redirect" => "abrir_caja.php"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Correo o contraseña incorrectos."
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error en el servidor: " . $e->getMessage()
    ]);
}
?>