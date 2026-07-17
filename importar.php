<?php
// Configuración igual a tu archivo conexion.php
$host = "127.0.0.1";
$user = "app_user"; 
$pass = "password123"; 
$db   = "sistema_ventas";

// Crear conexión
$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Leer el archivo .sql
$archivo_sql = 'Sistema_Ventas.sql';
if (!file_exists($archivo_sql)) {
    die("Error: No se encuentra el archivo Sistema_Ventas.sql");
}

$sql_content = file_get_contents($archivo_sql);

// Ejecutar el contenido
if ($mysqli->multi_query($sql_content)) {
    echo "<h1>¡Éxito!</h1><p>La base de datos se ha importado correctamente.</p>";
    echo "<p><strong>IMPORTANTE:</strong> Por seguridad, borra este archivo (importar.php) ahora mismo.</p>";
} else {
    echo "<h1>Error al importar:</h1><p>" . $mysqli->error . "</p>";
}
?>