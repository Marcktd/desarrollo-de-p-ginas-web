<?php
$host = "127.0.0.1";
$dbname = "sistema_ventas";
$username = "app_user"; 
$password = "password123"; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("🚨 Error de conexión: " . $e->getMessage());
}
?>