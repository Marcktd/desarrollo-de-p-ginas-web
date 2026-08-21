<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
</head>
<body>
    <h2>Acceso al Sistema</h2>
    <form action="validar.php" method="POST">
        <label>Correo:</label><br>
        <input type="email" name="correo" required><br><br>
        
        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>
        
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
