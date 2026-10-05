<?php
require_once __DIR__ . '/../../src/iniciar.php';
exigir_login();

$vazio    = ['nome' => '', 'crm' => '', 'especialidade' => '', 'telefone' => '', 'email' => ''];
$campos   = $vazio;
$mensagem = '';
$sucesso  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($vazio as $campo => $padrao) {
        $campos[$campo] = limpar($_POST[$campo] ?? '');
    }
    $campos['crm'] = mb_strtoupper($campos['crm']); // "crm/to 123" e "CRM/TO 123" passam a ser o mesmo CRM
    $telefone = formatar_telefone($campos['telefone']);

    if (!csrf_valido()) {
        $mensagem = 'Sessão expirada. Recarregue a página e tente novamente.';
    } elseif (in_array('', $campos, true)) {
        $mensagem = 'Todos os campos são obrigatórios.';
    } elseif (
        !tamanho_ok($campos['nome'], 100) || !tamanho_ok($campos['crm'], 30) ||
        !tamanho_ok($campos['especialidade'], 100) || !tamanho_ok($campos['email'], 100)
    ) {
        $mensagem = 'Algum campo ultrapassou o tamanho máximo permitido.';
    } elseif (!email_valido($campos['email'])) {
        $mensagem = 'E-mail inválido.';
    } elseif ($telefone === null) {
        $mensagem = 'Telefone inválido. Informe o DDD e o número.';
    } else {
        $medicos = new Medico(Conexao::obter());

        if ($medicos->crmExiste($campos['crm'])) {
            $mensagem = 'CRM já cadastrado.';
        } else {
            try {
                $campos['telefone'] = $telefone;
                $medicos->cadastrar($campos);
                $mensagem = 'Médico cadastrado com sucesso!';
                $sucesso  = true;
                $campos   = $vazio;
            } catch (mysqli_sql_exception $erro) {
                if ($erro->getCode() === 1062) {
                    $mensagem = 'CRM já cadastrado.';
                } else {
                    throw $erro;
                }
            }
        }
    }
}

$titulo  = 'Cadastro de Médicos';
$scripts = ['mascaras.js'];
require RAIZ . '/templates/cabecalho.php';
?>

<header class="page-header">
    <span class="ornament">Clínica Altesse</span>
    <h1>Cadastro de Médicos</h1>
    <div class="divider"></div>
</header>

<?php if ($mensagem !== ''): ?>
    <div class="mensagem-box <?= $sucesso ? '' : 'mensagem-erro' ?>" role="<?= $sucesso ? 'status' : 'alert' ?>">
        <?= e($mensagem) ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="POST">
        <?= campo_csrf() ?>

        <div class="form-group">
            <label for="nome">Nome completo</label>
            <input type="text" id="nome" name="nome" maxlength="100" placeholder="Digite o nome do médico"
                value="<?= e($campos['nome']) ?>" required>
        </div>

        <div class="form-group">
            <label for="crm">CRM</label>
            <input type="text" id="crm" name="crm" maxlength="30" placeholder="Ex: CRM/TO 12345"
                value="<?= e($campos['crm']) ?>" required>
        </div>

        <div class="form-group">
            <label for="especialidade">Especialidade</label>
            <input type="text" id="especialidade" name="especialidade" maxlength="100"
                placeholder="Ex: Dermatologia, Clínico Geral..." value="<?= e($campos['especialidade']) ?>" required>
        </div>

        <div class="form-group">
            <label for="telefone">Telefone</label>
            <input type="tel" id="telefone" name="telefone" maxlength="15" data-mascara="telefone"
                placeholder="(00) 00000-0000" value="<?= e($campos['telefone']) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" maxlength="100" placeholder="exemplo@email.com"
                value="<?= e($campos['email']) ?>" required>
        </div>

        <button type="submit" class="btn-submit">Cadastrar Médico</button>

    </form>

    <a class="link-lista" href="<?= e(url('medicos/listar.php')) ?>">→ Ver lista de médicos</a>
</div>

<?php require RAIZ . '/templates/rodape.php'; ?>
