<?php
session_start();
require_once '../conexion.php';

// Verificar que recibimos el ID de la venta
if (!isset($_GET['id'])) {
    die("Error: No se encontró el ticket.");
}
$id_venta = $_GET['id'];

// Consultamos la venta
$stmt = $conn->prepare("SELECT * FROM Venta WHERE id = :id");
$stmt->execute([':id' => $id_venta]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

// NUEVA CONSULTA: Aquí está la clave para ver los productos
$stmt_detalles = $conn->prepare("SELECT dv.*, p.nombre 
                                 FROM Detalle_venta dv
                                 JOIN Producto p ON dv.id_producto = p.id
                                 WHERE dv.id_venta = :id");
$stmt_detalles->execute([':id' => $id_venta]);
$detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Dentro de tu HTML, donde quieres que salgan los productos: -->
<div class="text-start">
    <?php foreach ($detalles as $item): ?>
        <div class="d-flex justify-content-between">
            <span><?php echo $item['cantidad']; ?> x <?php echo $item['nombre']; ?></span>
            <span>$<?php echo number_format($item['precio_unitario'] * $item['cantidad'], 2); ?></span>
        </div>
    <?php endforeach; ?>
    
</div>
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Venta #<?php echo $id_venta; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .ticket { max-width: 300px; margin: 20px auto; border: 1px solid #ccc; padding: 20px; }
    </style>
</head>
<body onload="window.print()"> 
    <div class="ticket text-center">
        <h4>SISTEMA DE VENTAS</h4>
        <p>Ticket #<?php echo str_pad($id_venta, 6, '0', STR_PAD_LEFT); ?></p>
        <p>Fecha: <?php echo $venta['fecha']; ?></p>
        <hr>
        
        <!-- ESTO ES LO QUE LE FALTABA A TU FORMATO ANTIGUO -->
        <div class="text-start">
            <?php foreach ($detalles as $item): ?>
                <div class="d-flex justify-content-between">
                    <span><?php echo $item['cantidad']; ?> x <?php echo $item['nombre']; ?></span>
                    <span>$<?php echo number_format($item['precio_unitario'] * $item['cantidad'], 2); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <hr>
        <div class="d-flex justify-content-between">
            <span><strong>TOTAL:</strong></span>
            <span><strong>$<?php echo number_format($venta['total'], 2); ?></strong></span>
        </div>
        <hr>
        <p>¡Gracias por su compra!</p>
        
        <!-- Botón que no saldrá en el PDF -->
        <div class="mt-4 no-print">
            <a href="realizar_venta.php" class="btn btn-sm btn-secondary">Nueva Venta</a>
        </div>
    </div>
</body>
</html>