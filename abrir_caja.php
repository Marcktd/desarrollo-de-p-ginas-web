<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Corregido: Ahora usa la variable exacta que guardó validar.php
if (!isset($_SESSION['usuario_id'])) {
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
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand fw-bold text-warning">Control de Caja</span>
        </div>
    </nav>

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title fw-bold text-secondary">Apertura de Turno</h3>
                        <!-- Corregido: Muestra correctamente el correo guardado en el login -->
                        <p class="text-muted">Cajero responsable: <strong class="text-dark"><?php echo htmlspecialchars($_SESSION['usuario_correo']); ?></strong></p>
                        <hr>

                        <form action="procesar_apertura.php" method="POST">
                            <div class="mb-3">
                                <label for="monto_inicial" class="form-label">Monto Inicial (Dinero base en caja):</label>
                                <div class="input-group">
                                    <span class="input-group-text">RD$</span>
                                    <input type="number" step="0.01" id="monto_inicial" name="monto_inicial" class="form-control" placeholder="Ej: 1500.00" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning w-100 fw-bold">Confirmar y Abrir Caja</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>