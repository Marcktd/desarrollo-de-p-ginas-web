<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cargamos la conexión que acabas de arreglar
require_once 'conexion.php'; 

// 2. Recogemos los datos de forma tradicional desde el formulario ($_POST)
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($correo) || empty($password)) {
    header("Location: index.php?error=vacio");
    exit();
}

try {
    // 3. Buscamos al usuario de forma segura con PDO usando $conn
    $sql = "SELECT id, correo FROM Usuario WHERE correo = :correo AND password = :password LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 4. Guardamos las variables de sesión correctas
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_correo'] = $user['correo']; 
        
        // 5. Redirigimos a la pantalla que necesitas
        header("Location: abrir_caja.php");
        exit();
    } else {
        // Si los datos están mal, regresa al index y muestra el mensaje rojo
        header("Location: index.php?error=incorrecto");
        exit();
    }
} catch (PDOException $e) {
    // Si la base de datos falla, nos dirá por qué en lugar de dar error 500
    die("Error en la consulta: " . $e->getMessage());
}
?>