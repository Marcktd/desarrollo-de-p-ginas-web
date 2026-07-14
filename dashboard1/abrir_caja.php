<?php
session_start();

// Si alguien intenta entrar aquí sin iniciar sesión, lo devolvemos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abrir Caja | Sistema de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <main class="container mt-5 flex-grow-1">
        <section class="card shadow-sm mx-auto" style="max-width: 500px;">
            <div class="card-body p-4">
                <h2 class="card-title text-center mb-4">Apertura de Turno</h2>
                
                <p class="text-muted">Cajero responsable: <strong><?php echo htmlspecialchars($_SESSION['usuario_correo']); ?></strong></p>
                <hr>

                <form action="caja/procesar_apertura.php" method="POST">
                    <div class="form-group mb-3">
                        <label for="monto_inicial">Monto Inicial (Dinero base en caja):</label>
                        <input type="number" step="0.01" name="monto_inicial" id="monto_inicial" class="form-control" placeholder="Ej: 1500.00" required>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 mt-2">Confirmar y Abrir Caja</button>
                </form>
            </div>
        </section>
    </main>

    <footer class="bg-dark text-white text-center py-4 mt-auto">
        <div class="container">
            <p class="mb-1">Universidad Abierta para Adultos (UAPA) &copy; 2026</p>
            <small class="text-white-50">Sistema de Ventas - Módulo de Caja</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>