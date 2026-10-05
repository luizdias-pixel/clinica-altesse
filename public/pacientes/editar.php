<?php
require_once __DIR__ . '/../../src/iniciar.php';
exigir_login();

// O id vem da URL (?id=3). Só aceitamos número inteiro positivo.
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    redirecionar('pacientes/listar.php');
}

$pacientes = new Paciente(Conexao::obter());
$paciente  = $pacientes->buscarPorId($id);

if ($paciente === null) {
    http_response_code(404);
    $titulo = 'Paciente não encontrado';
    require RAIZ . '/templates/cabecalho.php';
    echo '<div class="card card-centro"><span class="icone" aria-hidden="true">✕</span>'
        . '<h1>Paciente não encontrado</h1><div class="divider"></div>'
        . '<p>Esse registro não existe ou já foi removido.</p>'
        . '<a class="btn" href="' . e(url('pacientes/listar.php')) . '">Voltar à lista</a></div>';
    require RAIZ . '/templates/rodape.php';
    exit;
}

// Valores mostrados no formulário (começam com os do banco)
$campos = [
    'nome'         => $paciente['nome'],
    'telefone'     => $paciente['telefone'],
    'procedimento' => (string) $paciente['procedimento'],
    'data'         => (string) $paciente['data_agendamento'],
    'hora'         => substr((string) $paciente['hora_agendamento'], 0, 5), // 14:30:00 -> 14:30
];
$mensagem = '';
$sucesso  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['nome', 'telefone', 'procedimento', 'data', 'hora'] as $campo) {
        $campos[$campo] = limpar($_POST[$campo] ?? '');
    }
    $telefone = formatar_telefone($campos['telefone']);

    if (!csrf_valido()) {
        $mensagem = 'Sessão expirada. Recarregue a página e tente novamente.';
    } elseif ($campos['nome'] === '') {
        $mensagem = 'Informe o nome do paciente.';
    } elseif (!tamanho_ok($campos['nome'], 100) || !tamanho_ok($campos['procedimento'], 100)) {
        $mensagem = 'Algum campo ultrapassou o tamanho máximo permitido.';
    } elseif ($telefone === null) {
        $mensagem = 'Telefone inválido. Informe o DDD e o número.';
    } elseif ($campos['data'] !== '' && !data_valida($campos['data'])) {
        $mensagem = 'Data inválida.';
    } elseif ($campos['hora'] !== '' && !hora_valida($campos['hora'])) {
        $mensagem = 'Hora inválida.';
    } elseif ($campos['hora'] !== '' && $campos['data'] === '') {
        $mensagem = 'Informe a data do agendamento junto com a hora.';
    } else {
        try {
            $pacientes->atualizar($id, [
                'nome'             => $campos['nome'],
                'telefone'         => $telefone,
                'procedimento'     => $campos['procedimento'] === '' ? null : $campos['procedimento'],
                // Campo vazio vira NULL no banco (uma coluna DATE não aceita texto vazio)
                'data_agendamento' => $campos['data'] === '' ? null : $campos['data'],
                'hora_agendamento' => $campos['hora'] === '' ? null : $campos['hora'],
            ]);
            $campos['telefone'] = $telefone;
            $mensagem = 'Paciente atualizado com sucesso!';
            $sucesso  = true;
        } catch (mysqli_sql_exception $erro) {
            if ($erro->getCode() === 1062) {
                $mensagem = 'Já existe outro paciente com esse telefone.';
            } else {
                throw $erro;
            }
        }
    }
}

$titulo  = 'Editar Paciente';
$scripts = ['mascaras.js'];
require RAIZ . '/templates/cabecalho.php';
?>

<header class="page-header">
    <span class="ornament">Gestão</span>
    <h1>Editar Paciente</h1>
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
            <input type="text" id="nome" name="nome" maxlength="100" value="<?= e($campos['nome']) ?>" required>
        </div>

        <div class="form-group">
            <label for="telefone">Telefone</label>
            <input type="tel" id="telefone" name="telefone" maxlength="15" data-mascara="telefone"
                value="<?= e($campos['telefone']) ?>" required>
        </div>

        <div class="form-group">
            <label for="procedimento">Procedimento</label>
            <input type="text" id="procedimento" name="procedimento" maxlength="100" value="<?= e($campos['procedimento']) ?>">
        </div>

        <div class="form-group">
            <label for="data">Data do Agendamento</label>
            <input type="date" id="data" name="data" value="<?= e($campos['data']) ?>">
        </div>

        <div class="form-group">
            <label for="hora">Hora do Agendamento</label>
            <input type="time" id="hora" name="hora" value="<?= e($campos['hora']) ?>">
        </div>

        <button type="submit" class="btn-submit">Atualizar Paciente</button>

    </form>
</div>

<?php require RAIZ . '/templates/rodape.php'; ?>
