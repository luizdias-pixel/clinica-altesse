// Abre e fecha o menu no celular (botão com 3 risquinhos).
(function () {
    var botao = document.querySelector('.nav-toggle');
    var lista = document.getElementById('nav-links');

    if (!botao || !lista) {
        return;
    }

    botao.addEventListener('click', function () {
        var aberto = lista.classList.toggle('aberto');
        botao.setAttribute('aria-expanded', aberto ? 'true' : 'false');
        botao.setAttribute('aria-label', aberto ? 'Fechar menu' : 'Abrir menu');
    });
})();
