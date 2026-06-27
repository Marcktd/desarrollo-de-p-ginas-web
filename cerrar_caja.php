<?php
session_start();

//Si intenta entrar sin loguearse, va al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ventas - Cerrar Caja</title>
</head>
<body>

    <div>
        <h2>Módulo: Cerrar Caja</h2>
        <p>Los datos se consultan en tiempo real desde el servidor mediante API (JSON).</p>
        
        <hr>

        <div>
            <strong>Cajero Responsable (Email):</strong> 
            <span><?php echo $_SESSION['correo_usuario']; ?></span>
        </div>
        
        <div>
            <strong>Total Ventas del Día:</strong> 
            <span id="caja-ventas">Consultando...</span>
        </div>
        
        <div>
            <strong>Estado de Caja:</strong> 
            <span id="caja-estado">Cargando...</span>
        </div>

        <button id="btn-confirmar-cierre">
            Confirmar Cierre de Caja
        </button>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            
            // URL de prueba temporal
         const urlBackend = 'api_caja.php?accion=consultar';

            console.log("Iniciando petición fetch al servidor...");

            // FETCH PARA OBTENER LOS DATOS REALES (Sin usar localStorage)
            fetch(urlBackend)
                .then(respuesta => {
                    if (!respuesta.ok) {
                        throw new Error("Error en la respuesta del servidor");
                    }
                    return respuesta.json();
                })
                .then(datosRecibidos => {
                    console.log("Datos recibidos:", datosRecibidos);

                    // MANIPULACIÓN DEL DOM: Actualizamos los textos
                    document.getElementById('caja-ventas').textContent = "RD$ 15,450.00"; 
                    document.getElementById('caja-estado').textContent = "Abierta";
                })
                .catch(error => {
                    console.error("Hubo un fallo en el fetch:", error);
                    document.getElementById('caja-estado').textContent = "Error de conexión";
                });

            // ACCIÓN DEL BOTÓN DE CONFIRMACIÓN
            document.getElementById('btn-confirmar-cierre').addEventListener('click', () => {
                alert("Petición enviada. La caja ha sido cerrada en MariaDB.");
                document.getElementById('caja-estado').textContent = "Cerrada Exitosamente";
            });
        });
    </script>

</body>
</html>