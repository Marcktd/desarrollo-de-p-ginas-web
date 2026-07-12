<?php
<<<<<<< HEAD
$host = "127.0.0.1";
$dbname = "sistema_ventas"; 
$username = "root";         
$password = "";             
=======
$host = 'localhost';
$usuario = 'root';
$contraseña = '';
$base_de_datos = 'sistema_ventas';
>>>>>>> 421a06feab36c7052678234823aac9afcbcf8821

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Reemplazamos esta línea para ver el archivo y la línea exacta del error:
    die("🚨 Error: " . $e->getMessage() . "<br>📍 Archivo: " . $e->getFile() . "<br>🔢 Línea: " . $e->getLine());
}
?>