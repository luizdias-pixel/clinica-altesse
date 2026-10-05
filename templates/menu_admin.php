<nav class="nav" aria-label="Menu principal">
    <a class="nav-brand" href="<?= e(url('index.php')) ?>">Clínica <span>Altesse</span></a>

    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-links" aria-label="Abrir menu">
        <span></span><span></span><span></span>
    </button>

    <ul class="nav-links" id="nav-links">
        <li><a href="<?= e(url('index.php')) ?>" <?= menu_ativo('/index.php') ?>>Início</a></li>
        <li><a href="<?= e(url('pacientes/cadastrar.php')) ?>" <?= menu_ativo('/pacientes/cadastrar.php') ?>>Agendar</a></li>
        <li><a href="<?= e(url('agenda.php')) ?>" <?= menu_ativo('/agenda.php') ?>>Agenda</a></li>
        <li><a href="<?= e(url('pacientes/listar.php')) ?>" <?= menu_ativo('/pacientes/listar.php', '/pacientes/editar.php', '/pacientes/excluir.php') ?>>Pacientes</a></li>
        <li><a href="<?= e(url('medicos/cadastrar.php')) ?>" <?= menu_ativo('/medicos/cadastrar.php', '/medicos/listar.php') ?>>Médicos</a></li>
        <li><a class="nav-sair" href="<?= e(url('logout.php')) ?>">Sair</a></li>
    </ul>
</nav>
