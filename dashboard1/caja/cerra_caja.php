<?php
session_start();
require_once '../conexion.php'; // Ajusta la ruta a tu archivo de conexión

if (!isset($_SESSION['usuario_id'])) { header("Location: ../login.php"); exit(); }

$stmt = $conn->prepare("SELECT * FROM Caja WHERE id_usuario = ? AND estado = 'abierta' ORDER BY id DESC LIMIT 1");
$stmt->execute([$_SESSION['usuario_id']]);
$caja = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$caja) { die("No hay una caja abierta."); }

$stmtTotal = $conn->prepare("SELECT SUM(total) as vendido FROM Venta WHERE id_usuario = ? AND fecha >= ?");
$stmtTotal->execute([$_SESSION['usuario_id'], $caja['fecha_apertura']]);
$total_esperado = $stmtTotal->fetch(PDO::FETCH_ASSOC)['vendido'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $monto_final = $_POST['total_contado']; // Recibimos el total calculado por JS
    $upd = $conn->prepare("UPDATE Caja SET fecha_cierre = NOW(), monto_final = ?, estado = 'cerrada' WHERE id = ?");
    $upd->execute([$monto_final, $caja['id']]);
    header("Location: ../dashboard.php?msg=caja_cerrada");
    exit();
}

$denominaciones = [2000, 1000, 500, 100, 50, 10, 5, 1];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Arqueo de Caja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-body">
            <h4 class="card-title text-center">Arqueo de Caja</h4>
            <p class="text-center text-muted">Ventas registradas: <strong>$<?php echo number_format($total_esperado, 2); ?></strong></p>
            <hr>
            <form method="POST" id="formArqueo">
                <?php foreach ($denominaciones as $d): ?>
                <div class="row mb-2 align-items-center">
                    <div class="col-4"><strong>$<?php echo $d; ?></strong></div>
                    <div class="col-4"><input type="number" class="form-control count" data-val="<?php echo $d; ?>" value="0" min="0"></div>
                    <div class="col-4 text-end"><span class="subtotal" id="sub_<?php echo $d; ?>">$0.00</span></div>
                </div>
                <?php endforeach; ?>
                
                <hr>
                <h4 class="text-center">Total Contado: <span id="totalDisplay">$0.00</span></h4>
                <input type="hidden" name="total_contado" id="total_contado">
                
                <button type="submit" class="btn btn-success w-100 mt-3">Confirmar Cierre de Caja</button>
            </form>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.count');
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                let total = 0;
                inputs.forEach(i => {
                    const val = i.getAttribute('data-val');
                    const qty = i.value;
                    const sub = val * qty;
                    document.getElementById('sub_' + val).innerText = '$' + sub.toFixed(2);
                    total += sub;
                });
                document.getElementById('totalDisplay').innerText = '$' + total.toFixed(2);
                document.getElementById('total_contado').value = total;
            });
        });
    </script>
</body>
</html>