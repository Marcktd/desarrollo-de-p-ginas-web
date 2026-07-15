<?php
session_start();
// Importamos la conexión
require_once 'conexion.php'; 

// Recibimos y limpiamos los datos
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// 1. Validar campos vacíos
if (empty($correo) || empty($password)) {
    header("Location: ../login.php?error=vacio");
    exit();
}

try {
    // 2. Ejecutar consulta
    $sql = "SELECT id, nombre, correo FROM Usuario WHERE correo = :correo AND password = :password LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Verificar resultados
    if ($user) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_correo'] = $user['correo'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        
        header("Location: abrir_caja.php");
        exit();
    } else {
        // Credenciales incorrectas
        header("Location: ../login.php?error=incorrecto");
        exit();
    }

} catch (PDOException $e) {
    // 4. Captura de errores de conexión/consulta y redirección al login
    $mensaje_error = urlencode($e->getMessage());
    header("Location: ../login.php?error=db&msg=" . $mensaje_error);
    exit();
}
?>