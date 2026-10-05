// Monta o calendário da agenda com a biblioteca FullCalendar.
document.addEventListener('DOMContentLoaded', function () {
    var elemento = document.getElementById('calendario');
    if (!elemento) {
        return;
    }

    // Se a biblioteca não carregou (sem internet, por exemplo), avisa em vez de mostrar uma caixa vazia
    if (typeof FullCalendar === 'undefined') {
        elemento.textContent = 'Não foi possível carregar o calendário. Verifique sua conexão com a internet.';
        elemento.className = 'empty';
        return;
    }

    // No celular, a lista da semana é mais fácil de ler do que a grade do mês
    var celular = window.matchMedia('(max-width: 700px)').matches;

    var calendario = new FullCalendar.Calendar(elemento, {
        initialView: celular ? 'listWeek' : 'dayGridMonth',
        locale: 'pt-br',
        height: 'auto',
        events: elemento.dataset.eventos, // endereço do eventos.php, vindo do atributo data-eventos
        headerToolbar: celular
            ? { left: 'prev,next', center: 'title', right: 'listWeek,dayGridMonth' }
            : { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' }
    });

    calendario.render();
});
