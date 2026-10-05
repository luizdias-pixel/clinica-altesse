// Mostra o campo "Qual alergia?" só quando a resposta é "Sim".
(function () {
    var campo = document.getElementById('campoAlergia');
    var input = document.getElementById('possui_alergia');
    var radios = document.querySelectorAll('input[name="alergia"]');

    if (!campo || !input || radios.length === 0) {
        return;
    }

    function atualizar() {
        var sim = document.querySelector('input[name="alergia"][value="sim"]').checked;
        campo.classList.toggle('visible', sim);
        input.required = sim;
        if (!sim) {
            input.value = '';
        }
    }

    radios.forEach(function (radio) {
        radio.addEventListener('change', atualizar);
    });
    atualizar(); // ajusta o estado inicial (útil quando a página recarrega com erro)
})();
