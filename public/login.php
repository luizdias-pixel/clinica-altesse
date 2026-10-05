<?php
require_once __DIR__ . '/../src/iniciar.php';

// Quem já está logado não precisa ver a tela de login
if (usuario_logado()) {
    redirecionar('index.php');
}

$erro  = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = limpar($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? ''; // a senha não leva trim(): espaços podem fazer parte dela

    if (!csrf_valido()) {
        $erro = 'Sessão expirada. Recarregue a página e tente novamente.';
    } elseif ($email === '' || !is_string($senha) || $senha === '') {
        $erro = 'Informe e-mail e senha.';
    } else {
        $usuarios = new Usuario(Conexao::obter());
        $usuario  = $usuarios->autenticar($email, $senha);

        if ($usuario) {
            session_regenerate_id(true); // novo ID de sessão após o login (evita session fixation)
            $_SESSION['usuario_id']    = (int) $usuario['id_usuario'];
            $_SESSION['usuario_email'] = $usuario['email'];
            redirecionar('index.php');
        }

        // A mesma mensagem para "e-mail não existe" e "senha errada": não revela quais e-mails existem
        $erro = 'E-mail ou senha inválidos.';
    }
}

$titulo      = 'Login';
$menu        = 'nenhum';
$classe_body = 'pagina-login';
require RAIZ . '/templates/cabecalho.php';
?>

<div class="brand">
    <span class="ornament">Bem-vinda de volta</span>
    <h1>Clínica Altesse</h1>
    <div class="divider"></div>
</div>

<div class="card card-login">

    <p class="card-title">Acesse sua conta</p>

    <?php if ($erro !== ''): ?>
        <div class="erro-box" role="alert"><?= e($erro) ?></div>
    <?php endif; ?>

    <form method="POST">
        <?= campo_csrf() ?>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="seu@email.com"
                value="<?= e($email) ?>" autocomplete="username" required>
        </div>

        <div class="form-group">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" placeholder="••••••••"
                autocomplete="current-password" required>
        </div>

        <button type="submit" class="btn-submit">Entrar</button>
    </form>

    <div class="divider-or"><span>ou</span></div>

    <a href="<?= e(url('inicio.php')) ?>" class="btn-ghost">Entrar como Paciente</a>

</div>

<?php require RAIZ . '/templates/rodape.php'; ?>
