<?php
// Página inicial para o paciente (não precisa de login).
require_once __DIR__ . '/../src/iniciar.php';

$titulo = 'Início';
$menu   = 'publico';
require RAIZ . '/templates/cabecalho.php';
?>

<section class="hero">
    <span class="ornament">Beleza & Bem-estar</span>
    <h1>Clínica Altesse</h1>
    <div class="divider"></div>
    <p>Tratamentos estéticos de excelência pensados para realçar sua beleza natural com segurança e cuidado.</p>
</section>

<div class="section">

    <h2 class="section-label">Nossos Procedimentos</h2>

    <?php require RAIZ . '/templates/lista_procedimentos.php'; ?>

    <div class="cta-wrap">
        <a class="botao" href="<?= e(url('pacientes/cadastrar.php')) ?>">Agendar Atendimento</a>
    </div>

</div>

<?php require RAIZ . '/templates/rodape.php'; ?>
