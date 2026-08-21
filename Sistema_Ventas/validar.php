<?php
session_start();

// Asegúrate de que tu archivo de conexión se llame exactamente 'conexion.php'
include 'conexion.php'; 

$correo = $_POST['correo'];
$password = $_POST['password'];

$sql = "SELECT id, correo FROM Usuario WHERE correo = '$correo' AND password = '$password'";
$resultado = mysqli_query($conexion, $sql);

if (mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);
    
    $_SESSION['id_usuario'] = $fila['id'];
    $_SESSION['correo_usuario'] = $fila['correo']; 
    
    header("Location: abrir_caja.php"); 
    exit();
} else {
    echo "Correo o contraseña incorrectos. <br>";
    echo "<a href='login.php'>Volver a intentar</a>";
}
?>
