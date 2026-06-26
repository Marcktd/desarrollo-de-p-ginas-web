<?php
$host = 'localhost';
$usuario = 'root';
$contraseña = '';
$base_de_datos = 'Sistema_Ventas';

$conexion = mysqli_connect($host, $usuario, $contraseña, $base_de_datos);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
echo "Conexión exitosa";
?>
