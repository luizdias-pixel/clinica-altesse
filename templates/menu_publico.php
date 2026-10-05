<nav class="nav" aria-label="Menu principal">
    <a class="nav-brand" href="<?= e(url('inicio.php')) ?>">Clínica <span>Altesse</span></a>

    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-links" aria-label="Abrir menu">
        <span></span><span></span><span></span>
    </button>

    <ul class="nav-links" id="nav-links">
        <li><a href="<?= e(url('inicio.php')) ?>" <?= menu_ativo('/inicio.php') ?>>Início</a></li>
        <li><a href="<?= e(url('pacientes/cadastrar.php')) ?>" <?= menu_ativo('/pacientes/cadastrar.php') ?>>Agendar</a></li>
        <li><a href="<?= e(url('login.php')) ?>">Área do Usuário</a></li>
    </ul>
</nav>
