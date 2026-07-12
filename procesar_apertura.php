<?php
session_start();
require_once 'conexion.php';

// 1. DEPURACIÓN: Ver qué hay en la sesión
echo "<h3>Diagnóstico de Sesión:</h3>";
echo "ID de sesión: " . session_id() . "<br>";
echo "Usuario ID en sesión: " . (isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : "NO DEFINIDO") . "<br>";
echo "Correo en sesión: " . (isset($_SESSION['usuario_correo']) ? $_SESSION['usuario_correo'] : "NO DEFINIDO") . "<br>";

// 2. Si no está logueado, mostramos error en lugar de redirigir
if (!isset($_SESSION['usuario_id'])) {
    die("<b>ERROR:</b> No se detectó sesión de usuario. El sistema no sabe quién eres. Revisa tu archivo login.php.");
}

// 3. Verificar si el formulario envió el dato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['monto_inicial'])) {
    $monto = $_POST['monto_inicial'];
    $usuario_id = $_SESSION['usuario_id'];

    try {
        $sql = "INSERT INTO Apertura_caja (monto_inicial, is_usuario, fecha, estado) 
                VALUES (:monto, :usuario_id, NOW(), 'abierto')";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':monto' => $monto, ':usuario_id' => $usuario_id]);

        echo "<h2>¡Éxito! Caja abierta correctamente.</h2>";
        echo "<a href='dashboard.php'>Ir al Dashboard</a>";
        exit();

    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }
} else {
    echo "<b>Error:</b> No se recibió el monto inicial. Asegúrate de venir desde el formulario.";
}
?>