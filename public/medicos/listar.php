<?php
require_once __DIR__ . '/../../src/iniciar.php';
exigir_login();

$medicos = (new Medico(Conexao::obter()))->listar();

$titulo = 'Médicos';
require RAIZ . '/templates/cabecalho.php';
?>

<header class="page-header">
    <span class="ornament">Gestão</span>
    <h1>Lista de Médicos</h1>
    <div class="divider"></div>
</header>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>CRM</th>
                <th>Especialidade</th>
                <th>Telefone</th>
                <th>E-mail</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($medicos) === 0): ?>
                <tr>
                    <td colspan="5" class="empty">Nenhum médico cadastrado ainda.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($medicos as $dados): ?>
                    <tr>
                        <td data-label="Nome"><?= e($dados['nome']) ?></td>
                        <td data-label="CRM"><?= e($dados['crm']) ?></td>
                        <td data-label="Especialidade">
                            <span class="badge-especialidade"><?= e($dados['especialidade']) ?></span>
                        </td>
                        <td data-label="Telefone"><?= e($dados['telefone']) ?></td>
                        <td data-label="E-mail">
                            <a class="email-link" href="mailto:<?= e($dados['email']) ?>"><?= e($dados['email']) ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require RAIZ . '/templates/rodape.php'; ?>
