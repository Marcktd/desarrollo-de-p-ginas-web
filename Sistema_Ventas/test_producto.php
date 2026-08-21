<?php
require 'conexion.php';

$sql = "SELECT * FROM Producto";
$resultado = mysqli_query($conexion, $sql);

echo "<ul>";
while ($fila = mysqli_fetch_assoc($resultado)) {
    echo "<li>Producto: " . $fila['nombre'] . " - Precio: " . $fila['precio'] . " - Stock: " . $fila['stock'] . "</li>";
}
echo "</ul>";
?>
