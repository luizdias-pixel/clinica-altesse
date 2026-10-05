<?php
// Cadastro/agendamento de paciente. Pode ser usado por visitantes e por quem está logado.
require_once __DIR__ . '/../../src/iniciar.php';

$vazio = [
    'nome' => '',
    'cpf' => '',
    'tel' => '',
    'email' => '',
    'end' => '',
    'proc' => '',
    'alergia' => 'nao',
    'possui_alergia' => '',
];
$campos   = $vazio; // valores mostrados no formulário
$mensagem = '';
$sucesso  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1) Pega e limpa o que veio do formulário
    foreach (['nome', 'cpf', 'tel', 'email', 'end', 'proc', 'possui_alergia'] as $campo) {
        $campos[$campo] = limpar($_POST[$campo] ?? '');
    }
    $campos['alergia'] = (($_POST['alergia'] ?? '') === 'sim') ? 'sim' : 'nao';
    if ($campos['alergia'] === 'nao') {
        $campos['possui_alergia'] = '';
    }

    // 2) Valida (a primeira regra que falhar define a mensagem)
    $telefone = formatar_telefone($campos['tel']);

    if (!csrf_valido()) {
        $mensagem = 'Sessão expirada. Recarregue a página e tente novamente.';
    } elseif (
        $campos['nome'] === '' || $campos['cpf'] === '' || $campos['tel'] === '' ||
        $campos['email'] === '' || $campos['end'] === '' || $campos['proc'] === ''
    ) {
        $mensagem = 'Preencha todos os campos.';
    } elseif ($campos['alergia'] === 'sim' && $campos['possui_alergia'] === '') {
        $mensagem = 'Por favor, descreva a alergia do paciente.';
    } elseif (
        !tamanho_ok($campos['nome'], 100) || !tamanho_ok($campos['email'], 100) ||
        !tamanho_ok($campos['end'], 100) || !tamanho_ok($campos['proc'], 100) ||
        !tamanho_ok($campos['possui_alergia'], 300)
    ) {
        $mensagem = 'Algum campo ultrapassou o tamanho máximo permitido.';
    } elseif (!email_valido($campos['email'])) {
        $mensagem = 'E-mail inválido.';
        //} elseif (!cpf_valido($campos['cpf'])) {
        // $mensagem = 'CPF inválido.';
    } elseif ($telefone === null) {
        $mensagem = 'Telefone inválido. Informe o DDD e o número.';
    } else {

        // 3) Tudo certo: salva no banco
        $pacientes = new Paciente(Conexao::obter());
        $cpf = formatar_cpf($campos['cpf']);

        if ($pacientes->cpfExiste($cpf)) {
            $mensagem = 'Esse paciente já foi agendado!';
        } else {
            try {
                $pacientes->cadastrar([
                    'nome'           => $campos['nome'],
                    'email'          => $campos['email'],
                    'cpf'            => $cpf,
                    'telefone'       => $telefone,
                    'endereco'       => $campos['end'],
                    'procedimento'   => $campos['proc'],
                    'possui_alergia' => $campos['possui_alergia'],
                ]);
                $mensagem = 'Paciente agendado com sucesso!';
                $sucesso  = true;
                $campos   = $vazio;
            } catch (mysqli_sql_exception $erro) {
                // 1062 = valor duplicado em coluna UNIQUE (cpf ou telefone)
                if ($erro->getCode() === 1062) {
                    $mensagem = 'Já existe um paciente com esse CPF ou telefone.';
                } else {
                    throw $erro;
                }
            }
        }
    }
}

$titulo  = 'Agendamento de Paciente';
$menu    = usuario_logado() ? 'admin' : 'publico';
$scripts = ['mascaras.js', 'alergia.js'];
require RAIZ . '/templates/cabecalho.php';
?>

<header class="page-header">
    <span class="ornament">Clínica de Estética</span>
    <h1>Agendamento de Paciente</h1>
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
            <input type="text" id="nome" name="nome" maxlength="100" placeholder="Digite o nome do paciente"
                value="<?= e($campos['nome']) ?>" autocomplete="name" required>
        </div>

        <div class="form-group">
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" maxlength="14" inputmode="numeric" data-mascara="cpf"
                placeholder="000.000.000-00" value="<?= e($campos['cpf']) ?>" required>
        </div>

        <div class="form-group">
            <label for="tel">Telefone</label>
            <input type="tel" id="tel" name="tel" maxlength="15" data-mascara="telefone"
                placeholder="(00) 00000-0000" value="<?= e($campos['tel']) ?>" autocomplete="tel" required>
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" maxlength="100" placeholder="exemplo@email.com"
                value="<?= e($campos['email']) ?>" autocomplete="email" required>
        </div>

        <div class="form-group">
            <label for="end">Endereço</label>
            <input type="text" id="end" name="end" maxlength="100" placeholder="Rua, número, bairro"
                value="<?= e($campos['end']) ?>" autocomplete="street-address" required>
        </div>

        <div class="form-group">
            <label for="proc">Procedimento</label>
            <input type="text" id="proc" name="proc" maxlength="100" list="lista-procedimentos"
                placeholder="Ex: Limpeza de pele, Botox..." value="<?= e($campos['proc']) ?>" required>
            <datalist id="lista-procedimentos">
                <?php foreach (procedimentos() as $proc): ?>
                    <option value="<?= e($proc['nome']) ?>">
                    <?php endforeach; ?>
            </datalist>
        </div>

        <div class="form-group">
            <span class="label">Possui alergia?</span>
            <div class="radio-group">
                <label class="radio-option">
                    <input type="radio" name="alergia" value="nao" <?= $campos['alergia'] === 'nao' ? 'checked' : '' ?>>
                    Não
                </label>
                <label class="radio-option">
                    <input type="radio" name="alergia" value="sim" <?= $campos['alergia'] === 'sim' ? 'checked' : '' ?>>
                    Sim
                </label>
            </div>
        </div>

        <div class="alergia-field" id="campoAlergia">
            <div class="form-group">
                <label for="possui_alergia">Qual alergia?</label>
                <input type="text" id="possui_alergia" name="possui_alergia" maxlength="300"
                    placeholder="Descreva a alergia" value="<?= e($campos['possui_alergia']) ?>">
            </div>
        </div>

        <button type="submit" class="btn-submit">Agendar Consulta</button>

    </form>
</div>

<?php require RAIZ . '/templates/rodape.php'; ?>