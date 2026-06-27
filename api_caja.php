<?php
session_start();
// 1. Incluir conexión
include 'conexion.php'; 

// 2. Definir que la respuesta será JSON
header('Content-Type: application/json');

// 3. Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// 4. Determinar la acción (consultar datos o cerrar caja)
$accion = $_GET['accion'] ?? '';

if ($accion == 'consultar') {
    $stmt = $conexion->prepare("SELECT * FROM apertura_caja WHERE estado = 'abierto' AND id_usuario = ?");
    $stmt->bind_param("i", $_SESSION['id_usuario']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $datos = $resultado->fetch_assoc();
    
    echo json_encode($datos);

} elseif ($accion == 'cerrar') {
    $stmt = $conexion->prepare("UPDATE apertura_caja SET estado = 'cerrado' WHERE estado = 'abierto' AND id_usuario = ?");
    $stmt->bind_param("i", $_SESSION['id_usuario']);
    $stmt->execute();

    // Verificamos si se actualizó al menos una fila
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Caja cerrada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No hay caja abierta para cerrar']);
    }
}


?>