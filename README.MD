# Sistema de Ventas - Proyecto Integrador

## Descripción
Este sistema permite la gestión de ventas y cuenta con un módulo específico para el **Cierre de Caja**, consultando los datos en tiempo real mediante una API conectada a una base de datos MariaDB.

## Tecnologías Utilizadas
- **Backend:** PHP
- **Base de Datos:** MariaDB / MySQL
- **Frontend:** HTML, CSS y JavaScript (Fetch API)

## Instrucciones de Instalación
1. **Clonar el repositorio:** Descarga o clona el código fuente en tu servidor local (ej. carpeta `htdocs` en XAMPP).
2. **Base de Datos:**
   - Abre phpMyAdmin.
   - Crea una base de datos llamada `Sistema_Ventas`.
   - Importa el archivo `Sistema_Ventas.sql` incluido en el repositorio.
3. **Configuración:**
   - Abre el archivo `conexion.php`.
   - Verifica que las credenciales (usuario, contraseña y base de datos) coincidan con tu configuración local.
4. **Ejecución:**
   - Accede al sistema desde tu navegador mediante `localhost/nombre-de-tu-carpeta/index.php`.