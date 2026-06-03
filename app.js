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
