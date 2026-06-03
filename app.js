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
