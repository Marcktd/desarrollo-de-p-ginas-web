<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cargamos la conexión
require_once 'conexion.php'; 

// 2. Recogemos los datos del formulario
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Ajuste: si falla, regresa a login.php, no a index.php
if (empty($correo) || empty($password)) {
    header("Location: login.php?error=vacio");
    exit();
}

try {
    // 3. Buscamos al usuario de forma segura
    $sql = "SELECT id, correo FROM Usuario WHERE correo = :correo AND password = :password LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 4. Guardamos las variables de sesión
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_correo'] = $user['correo']; 
        
        // 5. Redirigimos al flujo de trabajo del cajero
       header("Location: dashboard1/abrir_caja.php");
        exit();
    } else {
        // Ajuste: si los datos están mal, regresa a login.php
        header("Location: login.php?error=incorrecto");
        exit();
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>