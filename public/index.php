<?php
require_once __DIR__ . '/../src/iniciar.php';
exigir_login();

$titulo = 'Painel';
require RAIZ . '/templates/cabecalho.php';
?>

<section class="hero">
    <span class="ornament">Painel Administrativo</span>
    <h1>Bem-vinda à Clínica Altesse</h1>
    <div class="divider"></div>
</section>

<div class="section">

    <h2 class="section-label"> </h2>

    <div class="atalhos">

        <div class="atalho-card">
            <span class="atalho-icon" aria-hidden="true"></span>
            <h3>Pacientes</h3>
            <p>Visualize todos os pacientes cadastrados.</p>
            <a class="btn-atalho" href="<?= e(url('pacientes/listar.php')) ?>">Acessar</a>
        </div>

        <div class="atalho-card">
            <span class="atalho-icon" aria-hidden="true"></span>
            <h3>Cadastrar</h3>
            <p>Adicionar um novo paciente ao sistema.</p>
            <a class="btn-atalho" href="<?= e(url('pacientes/cadastrar.php')) ?>">Cadastrar</a>
        </div>

        <div class="atalho-card">
            <span class="atalho-icon" aria-hidden="true"></span>
            <h3>Agenda</h3>
            <p>Consulte os agendamentos da semana.</p>
            <a class="btn-atalho" href="<?= e(url('agenda.php')) ?>">Ver Agenda</a>
        </div>

    </div>

    <h2 class="section-label">Procedimentos da Clínica</h2>

    <?php require RAIZ . '/templates/lista_procedimentos.php'; ?>

</div>

<?php require RAIZ . '/templates/rodape.php'; ?>