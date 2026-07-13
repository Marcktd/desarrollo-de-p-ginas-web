<?php
require_once '../../conexion.php';
$q = $_GET['q'];
$stmt = $conn->prepare("SELECT * FROM Producto WHERE nombre LIKE :q");
$stmt->execute(['q' => "%$q%"]);

while ($row = $stmt->fetch()) {
    echo "<div class='p-2 border-bottom' onclick='seleccionar({$row['id']})'>
            {$row['nombre']} - {$row['precio']}
          </div>";
}
?>