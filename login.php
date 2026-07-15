<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Personal | ISW-306</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <main class="container mt-5" style="max-width: 450px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="text-center mb-4">Acceso Personal</h2>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php 
                        if ($_GET['error'] == 'vacio') echo "Debes completar todos los campos.";
                        elseif ($_GET['error'] == 'incorrecto') echo "Correo o contraseña incorrectos.";
                        elseif ($_GET['error'] == 'db') echo "<strong>Error de BD:</strong> " . htmlspecialchars($_GET['msg']);
                        ?>
                    </div>
                <?php endif; ?>

                <form action="dashboard1/validar.php" method="POST">
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" id="correo" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>

                <a href="index.php" class="btn btn-link w-100 mt-2">Volver al inicio</a>
            </div>
        </div>
    </main>
    
    <footer class="bg-dark text-white text-center py-4 mt-auto">
        <div class="container">
            <p class="mb-1">Universidad Abierta para Adultos (UAPA) &copy; 2026</p>
            <small class="text-white-50">Sistema de Ventas - Módulo de login</small>
        </div>
    </footer> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>