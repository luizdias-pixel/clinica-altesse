<?php
// Excluir paciente em 2 passos:
//   1) GET  -> mostra a tela "Tem certeza?" (não apaga nada)
//   2) POST -> apaga de verdade (com token CSRF)
// Apagar por link comum (GET) é perigoso: qualquer página/e-mail poderia disparar a exclusão.
require_once __DIR__ . '/../../src/iniciar.php';
exigir_login();

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    redirecionar('pacientes/listar.php');
}

$pacientes = new Paciente(Conexao::obter());
$paciente  = $pacientes->buscarPorId($id);

$estado = $paciente ? 'confirmar' : 'nao_encontrado'; // confirmar | sucesso | erro | nao_encontrado

if ($paciente && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (csrf_valido() && $pacientes->excluir($id)) {
        $estado = 'sucesso';
        header('Refresh: 3; url=' . url('pacientes/listar.php')); // volta para a lista em 3 segundos
    } else {
        $estado = 'erro';
    }
}

$titulo = 'Excluir Paciente';
require RAIZ . '/templates/cabecalho.php';
?>

<div class="center">
    <div class="card card-centro">

        <?php if ($estado === 'confirmar'): ?>

            <span class="icone" aria-hidden="true">⚠️</span>
            <h1>Excluir paciente?</h1>
            <div class="divider"></div>
            <p>Você está prestes a excluir <strong><?= e($paciente['nome']) ?></strong>.<br>Essa ação não pode ser desfeita.</p>

            <form method="POST" class="form-confirmar">
                <?= campo_csrf() ?>
                <a class="btn btn-secundario" href="<?= e(url('pacientes/listar.php')) ?>">Cancelar</a>
                <button type="submit" class="btn btn-perigo">Sim, excluir</button>
            </form>

        <?php elseif ($estado === 'sucesso'): ?>

            <span class="icone" aria-hidden="true">✓</span>
            <h1>Paciente Excluído</h1>
            <div class="divider"></div>
            <p>O registro foi removido com sucesso.<br>Você será redirecionado em instantes.</p>
            <div class="progress-wrap">
                <div class="progress-bar"></div>
            </div>
            <span class="redirect-msg">Redirecionando para a lista de pacientes...</span>
            <br>
            <a class="btn" href="<?= e(url('pacientes/listar.php')) ?>">Ir agora</a>

        <?php elseif ($estado === 'erro'): ?>

            <span class="icone" aria-hidden="true">✕</span>
            <h1>Erro ao Excluir</h1>
            <div class="divider"></div>
            <p>Não foi possível remover o paciente.<br>Tente novamente ou contate o suporte.</p>
            <a class="btn" href="<?= e(url('pacientes/listar.php')) ?>">Voltar à lista</a>

        <?php else: ?>

            <span class="icone" aria-hidden="true">✕</span>
            <h1>Paciente não encontrado</h1>
            <div class="divider"></div>
            <p>Esse registro não existe ou já foi removido.</p>
            <a class="btn" href="<?= e(url('pacientes/listar.php')) ?>">Voltar à lista</a>

        <?php endif; ?>

    </div>
</div>

<?php require RAIZ . '/templates/rodape.php'; ?>
