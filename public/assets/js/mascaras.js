// Máscaras de digitação. Para usar, coloque data-mascara="cpf" ou data-mascara="telefone" no <input>.
// Isto é só conforto para quem digita: a validação de verdade é feita no PHP.

function somenteDigitos(valor) {
    return valor.replace(/\D/g, '');
}

function mascaraCpf(valor) {
    var d = somenteDigitos(valor).slice(0, 11);
    var resultado = d.slice(0, 3);
    if (d.length > 3) resultado += '.' + d.slice(3, 6);
    if (d.length > 6) resultado += '.' + d.slice(6, 9);
    if (d.length > 9) resultado += '-' + d.slice(9, 11);
    return resultado;
}

function mascaraTelefone(valor) {
    var d = somenteDigitos(valor).slice(0, 11);
    if (d.length === 0) return '';
    if (d.length <= 2) return '(' + d;

    var ddd = d.slice(0, 2);
    var resto = d.slice(2);
    if (resto.length <= 4) return '(' + ddd + ') ' + resto;

    var corte = d.length === 11 ? 5 : 4; // celular: 5 dígitos antes do hífen; fixo: 4
    return '(' + ddd + ') ' + resto.slice(0, corte) + '-' + resto.slice(corte);
}

var mascaras = { cpf: mascaraCpf, telefone: mascaraTelefone };

document.querySelectorAll('[data-mascara]').forEach(function (campo) {
    var aplicar = mascaras[campo.dataset.mascara];
    if (!aplicar) return;

    campo.addEventListener('input', function () {
        campo.value = aplicar(campo.value);
    });
    campo.value = aplicar(campo.value); // formata também o valor que já veio preenchido
});
