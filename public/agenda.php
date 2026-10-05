<?php
require_once __DIR__ . '/../src/iniciar.php';
exigir_login();

$titulo = 'Agenda';

// Biblioteca FullCalendar (carregada da internet, como no projeto original)
$head_extra = '
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/locales/pt-br.global.min.js"></script>';
$scripts = ['agenda.js'];

require RAIZ . '/templates/cabecalho.php';
?>

<header class="page-header">
    <span class="ornament">Organização</span>
    <h1>Agenda da Clínica</h1>
    <div class="divider"></div>
</header>

<div class="cal-wrapper">
    <div id="calendario" data-eventos="<?= e(url('eventos.php')) ?>"></div>
</div>

<?php require RAIZ . '/templates/rodape.php'; ?>
