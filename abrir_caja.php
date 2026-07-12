<?php
session_start();

// Si alguien intenta entrar aquí sin iniciar sesión, lo devolvemos al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abrir Caja</title>
    <!-- Incluimos Bootstrap 5 para mantener la consistencia estética del proyecto -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h2>Apertura de Turno - Caja</h2>
    
    <p>Cajero responsable: <strong><?php echo $_SESSION['correo_usuario']; ?></strong></p>
    <hr>

    <form action="procesar_apertura.php" method="POST">
        <label>Monto Inicial (Dinero base en caja):</label><br>
        <input type="number" step="0.01" name="monto_inicial" placeholder="Ej: 1500.00" required><br><br>
        
        <button type="submit">Confirmar y Abrir Caja</button>
    </form>
</body>
</html>