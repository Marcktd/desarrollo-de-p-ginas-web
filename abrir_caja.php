<?php
session_start();
echo "ID de sesión en abrir_caja: " . session_id() . "<br>";
echo "Usuario ID en sesión: " . (isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : "No definido");
// Comenta el resto de la página temporalmente para ver esto limpio.
// Verifica sesión con la clave correcta (usuario_id)
if (!isset($_SESSION['usuario_id'])) {
    // Si no está logueado, lo manda al index, no a login.php
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Abrir Caja</title>
</head>
<body>
    <h2>Apertura de Turno - Caja</h2>
    
    <p>Cajero responsable: <strong><?php echo $_SESSION['usuario_correo']; ?></strong></p>
    <hr>

    <form action="procesar_apertura.php" method="POST">
        
        <label>Monto Inicial (Dinero base en caja):</label><br>
        <input type="number" step="0.01" name="monto_inicial" placeholder="Ej: 1500.00" required><br><br>
        
        <button type="submit">Confirmar y Abrir Caja</button>
    </form>
</body>
</html>
