/**
 * 1 - MÓDULO DE VALIDACIONES
 * Creado por: Uriel Tejada Padilla 
 * Funciones encargadas de verificar la integridad de los datos
 * en tiempo real antes de permitir acciones en el sistema.
 */

/**
 * Verifica si un campo de texto está vacío o contiene solo espacios.
 * @param {string} valor - El texto ingresado.
 * @returns {boolean} - Retorna true si tiene contenido válido, false si está vacío.
 */
function ValidarElCampoVacio(valor) {
    if (valor.trim() === '') {
        return false; 
    }
    return true; 
}
/**
 * Valida si el formato de un correo electrónico es correcto.
 * @param {string} email - Correo electrónico a evaluar.
 * @returns {boolean} - Retorna true si el correo es válido, false si no.
 */
function ValidarElCorreo(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
    if (regex.test(email)) {
        return true;
    }
    return false;
}
/**
 * @param {string} monto
 * @returns {boolean}
 */
function ValidarElMonto(monto) {
    if (monto.trim() === '') {
        return false;
    }
    
    const numero = Number(monto);

    // Nota de integracion: Se mantiene "numero < 0" para permitir abrir caja con $0 pesos.
    
    if (isNaN(numero) || numero < 0) {
        return false;
    }
    
    return true;
}
/**
 * 2 - MÓDULO DE OPERACIONES DE CAJA
 * Creado por: Keila Nicole Payamps Rosario 
 * Controla el flujo de apertura, cierre y control de caja del sistema.
 */

// Variables globales

//Lógica de caja

let cajaAbierta = false;
let montoEnCaja = 0;
let totalVentasDelDia = 0;
//Intenta abrir la caja registrando un monto inicial válido.
function abrirCaja(montoIngresado) {
    const esValido = ValidarElMonto(montoIngresado);

    if (esValido === false) {
        return { exito: false, mensaje: "Error: El monto inicial no es válido o está vacío." };
    }

    montoEnCaja = Number(montoIngresado);
    cajaAbierta = true;

    return { exito: true, mensaje: `Caja abierta exitosamente con $${montoEnCaja}.` };
}
 
//Cierra la caja del día validando la contraseña administrativa.
function cerrarCaja(contraseñaIngresada) {
    const claveNoVacia = ValidarElCampoVacio(contraseñaIngresada);

    if (claveNoVacia === false) {
        return { exito: false, mensaje: "Error: Debe ingresar una contraseña para cerrar." };
    }

    // Credencial estática temporal de administración
    const claveCorrecta = "admin123";
    if (contraseñaIngresada !== claveCorrecta) {
        return { exito: false, mensaje: "Error: Contraseña incorrecta." };
    }

    const resumen = `Cierre exitoso. Monto inicial: $${montoEnCaja} | Ventas del día: $${totalVentasDelDia}.`;
    cajaAbierta = false;

    return { exito: true, mensaje: resumen };
}
// GESTOR DE PERSISTENCIA DE DATOS (LocalStorage)
/**
 * Guarda un arreglo de objetos en el LocalStorage del navegador.
 * @param {string} llave - El nombre clave para identificar los datos guardados.
 * @param {Array} arreglo - El arreglo de objetos de JavaScript que se desea persistir.
 */
function guardarEnLocalStorage(llave, arreglo) {
    try {
        // LocalStorage solo almacena texto plano, por eso convertimos el arreglo con stringify
        const datosTexto = JSON.stringify(arreglo);
        localStorage.setItem(llave, datosTexto);
        console.log(`[Persistencia] Datos guardados exitosamente bajo la llave: "${llave}"`);
    } catch (error) {
        console.error("[Persistencia] Error crítico al guardar en LocalStorage:", error);
    }
}

/**
 * Recupera un arreglo de objetos desde el LocalStorage.
 * @param {string} llave - El nombre de la clave que se desea buscar.
 * @returns {Array} - Devuelve el arreglo con los datos o un arreglo vacío [] si no hay nada.
 */
function obtenerDesdeLocalStorage(llave) {
    try {
        const datosTexto = localStorage.getItem(llave);
        // Si existen datos en texto, los transformamos de vuelta a un objeto/arreglo de JS
        return datosTexto ? JSON.parse(datosTexto) : [];
    } catch (error) {
        console.error("[Persistencia] Error crítico al obtener de LocalStorage:", error);
        return [];
    }
}