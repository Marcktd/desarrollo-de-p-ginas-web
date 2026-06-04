/**
 * @param {string} valor
 * @returns {boolean}
 */
function ValidarElCampoVacio(valor) {
    if (valor.trim() === '') {
        return false; 
    }
    return true; 
}
/**
 * @param {string} email
 * @returns {boolean}
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
    
    if (isNaN(numero) || numero < 0) {
        return false;
    }
    
    return true;
}

//Lógica de caja

let cajaAbierta = false;
let montoEnCaja = 0;
let totalVentasDelDia = 0;

function abrirCaja(montoIngresado) {
    const esValido = ValidarElMonto(montoIngresado);

    if (esValido === false) {
        return { exito: false, mensaje: "Error: El monto inicial no es válido o está vacío." };
    }

    montoEnCaja = Number(montoIngresado);
    cajaAbierta = true;

    return { exito: true, mensaje: `Caja abierta exitosamente con $${montoEnCaja}.` };
}

function cerrarCaja(contraseñaIngresada) {
    const claveNoVacia = ValidarElCampoVacio(contraseñaIngresada);

    if (claveNoVacia === false) {
        return { exito: false, mensaje: "Error: Debe ingresar una contraseña para cerrar." };
    }

    const claveCorrecta = "admin123";
    if (contraseñaIngresada !== claveCorrecta) {
        return { exito: false, mensaje: "Error: Contraseña incorrecta." };
    }

    const resumen = `Cierre exitoso. Monto inicial: $${montoEnCaja} | Ventas del día: $${totalVentasDelDia}.`;
    cajaAbierta = false;

    return { exito: true, mensaje: resumen };
}