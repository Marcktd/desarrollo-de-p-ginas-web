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