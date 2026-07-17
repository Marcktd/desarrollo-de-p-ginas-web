<?php
session_start();
require_once '../conexion.php';

// Consulta para obtener las ventas totales agrupadas por fecha
$sql = "SELECT DATE(fecha) as fecha, COUNT(id) as total_ventas, SUM(total) as ingreso_total 
        FROM Venta 
        GROUP BY DATE(fecha) 
        ORDER BY fecha DESC";
$ventas = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container">
        <h2 class="mb-4">Reporte Diario de Ventas</h2>
        <table class="table table-striped bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Cantidad de Ventas</th>
                    <th>Ingreso Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $row): ?>
                <tr>
                    <td><?php echo $row['fecha']; ?></td>
                    <td><?php echo $row['total_ventas']; ?></td>
                    <td class="fw-bold">$<?php echo number_format($row['ingreso_total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="../caja/procesar_apertura.php" class="btn btn-secondary">Volver al Punto de Venta</a>
    </div>
</body>
</html>