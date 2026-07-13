<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso Personal | ISW-306</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container mt-5" style="max-width: 450px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="text-center mb-4">Acceso Personal</h2>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class='alert alert-danger'>Credenciales incorrectas.</div>
                <?php endif; ?>

          <form action="validar.php" method="POST">
    <div class="mb-3">
        <label for="correo">Correo Electrónico</label>
        <input type="email" name="correo" id="correo" class="form-control" required autofocus>
    </div>
    <div class="mb-3">
        <label for="password">Contraseña</label>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>