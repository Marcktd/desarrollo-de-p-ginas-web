<?php
session_start();
// Como ahora validar.php y conexion.php están en la misma carpeta (dashboard1), la importación es directa
require_once 'conexion.php'; 

$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($correo) || empty($password)) {
    // Subimos un nivel (../) para encontrar login.php en la raíz del proyecto
    header("Location: ../login.php?error=vacio");
    exit();
}

try {
    // La tabla es 'Usuario' y las columnas 'correo' y 'password'
    $sql = "SELECT id, nombre, correo FROM Usuario WHERE correo = :correo AND password = :password LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_correo'] = $user['correo'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        
        // Redireccionamos directo porque abrir_caja.php está en esta misma carpeta (dashboard1)
        header("Location: abrir_caja.php");
        exit();
    } else {
        // Subimos un nivel (../) para encontrar login.php en la raíz del proyecto
        header("Location: ../login.php?error=incorrecto");
        exit();
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>