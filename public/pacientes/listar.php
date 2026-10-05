<?php
require_once __DIR__ . '/../../src/iniciar.php';
exigir_login();

$pacientes = (new Paciente(Conexao::obter()))->listar();

$titulo = 'Pacientes';
require RAIZ . '/templates/cabecalho.php';
?>

<header class="page-header">
    <span class="ornament">Gestão</span>
    <h1>Lista de Pacientes</h1>
    <div class="divider"></div>
</header>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Telefone</th>
                <th>Procedimento</th>
                <th>Alergia</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($pacientes) === 0): ?>
                <tr>
                    <td colspan="6" class="empty">Nenhum paciente cadastrado ainda.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($pacientes as $dados): ?>
                    <tr>
                        <td data-label="Nome"><?= e($dados['nome']) ?></td>
                        <td data-label="CPF"><?= e($dados['cpf']) ?></td>
                        <td data-label="Telefone"><?= e($dados['telefone']) ?></td>
                        <td data-label="Procedimento"><?= e($dados['procedimento']) ?></td>
                        <td data-label="Alergia">
                            <?php if (!empty($dados['possui_alergia'])): ?>
                                <span class="badge badge-sim"><?= e($dados['possui_alergia']) ?></span>
                            <?php else: ?>
                                <span class="badge badge-nao">Não</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Ações">
                            <div class="acoes">
                                <a class="btn-editar" href="<?= e(url('pacientes/editar.php?id=' . (int) $dados['id_paciente'])) ?>">Editar</a>
                                <a class="btn-excluir" href="<?= e(url('pacientes/excluir.php?id=' . (int) $dados['id_paciente'])) ?>">Excluir</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require RAIZ . '/templates/rodape.php'; ?>
