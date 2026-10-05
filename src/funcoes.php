<?php
// Funções pequenas e reutilizáveis, usadas em várias páginas.

/* ---------- Saída segura (contra XSS) ---------- */

// Use e() sempre que mostrar na tela um dado vindo do usuário ou do banco.
function e($texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

/* ---------- Links ---------- */

// Monta o link de uma página a partir da pasta public/. Ex.: url('pacientes/listar.php')
function url(string $caminho = ''): string
{
    static $base = null;

    if ($base === null) {
        $base = $GLOBALS['config']['url_base'] ?? null;

        if ($base === null) {
            // Descobre a pasta onde public/ está na URL, comparando duas informações da página atual:
            //   SCRIPT_FILENAME = onde o arquivo está no disco  (C:/xampp/htdocs/clinica/public/pacientes/listar.php)
            //   SCRIPT_NAME     = o endereço dele na URL        (/clinica/public/pacientes/listar.php)
            // A parte que sobra na frente da URL, antes de "/pacientes/listar.php", é a base.
            $base    = '';
            $arquivo = str_replace('\\', '/', (string) realpath($_SERVER['SCRIPT_FILENAME'] ?? ''));
            $public  = str_replace('\\', '/', (string) realpath(RAIZ . '/public'));
            $script  = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

            // strtolower: o Windows não diferencia "C:" de "c:"
            if ($arquivo !== '' && $public !== '' && str_starts_with(strtolower($arquivo), strtolower($public) . '/')) {
                $relativo = substr($arquivo, strlen($public));   // /pacientes/listar.php
                if (str_ends_with(strtolower($script), strtolower($relativo))) {
                    $base = substr($script, 0, strlen($script) - strlen($relativo));
                }
            }
        }
        $base = rtrim($base, '/');
    }

    return $base . '/' . ltrim($caminho, '/');
}

function redirecionar(string $caminho): void
{
    header('Location: ' . url($caminho));
    exit;
}

// Devolve ' aria-current="page"' se a página atual combina com algum trecho (para destacar o menu)
function menu_ativo(string ...$trechos): string
{
    $atual = $_SERVER['SCRIPT_NAME'] ?? '';
    foreach ($trechos as $trecho) {
        if (str_ends_with($atual, $trecho)) {
            return ' aria-current="page"';
        }
    }
    return '';
}

/* ---------- Login ---------- */

function usuario_logado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function exigir_login(): void
{
    if (!usuario_logado()) {
        redirecionar('login.php');
    }
}

/* ---------- CSRF: garante que o formulário foi enviado pelo nosso próprio site ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function campo_csrf(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valido(): bool
{
    $enviado = $_POST['csrf'] ?? '';
    return is_string($enviado)
        && isset($_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $enviado);
}

/* ---------- Validação e limpeza de dados ---------- */

// Pega um valor de $_POST/$_GET como texto, sem espaços nas pontas
function limpar($valor): string
{
    return is_string($valor) ? trim($valor) : '';
}

function so_digitos(string $texto): string
{
    return preg_replace('/\D/', '', $texto);
}

function tamanho_ok(string $texto, int $max): bool
{
    return mb_strlen($texto) <= $max;
}

function email_valido(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function cpf_valido(string $cpf): bool
{
    $d = so_digitos($cpf);

    if (strlen($d) !== 11 || preg_match('/^(\d)\1{10}$/', $d)) {
        return false; // tamanho errado ou todos os dígitos iguais
    }

    // Confere os dois dígitos verificadores
    for ($posicao = 9; $posicao < 11; $posicao++) {
        $soma = 0;
        for ($i = 0; $i < $posicao; $i++) {
            $soma += (int) $d[$i] * (($posicao + 1) - $i);
        }
        $digito = ((10 * $soma) % 11) % 10;
        if ((int) $d[$posicao] !== $digito) {
            return false;
        }
    }
    return true;
}

function formatar_cpf(string $cpf): string
{
    $d = so_digitos($cpf);
    return substr($d, 0, 3) . '.' . substr($d, 3, 3) . '.' . substr($d, 6, 3) . '-' . substr($d, 9, 2);
}

// Devolve "(63) 99999-9999" ou null se o telefone não tiver 10 ou 11 dígitos
function formatar_telefone(string $telefone): ?string
{
    $d = so_digitos($telefone);

    if (strlen($d) === 11) {
        return '(' . substr($d, 0, 2) . ') ' . substr($d, 2, 5) . '-' . substr($d, 7);
    }
    if (strlen($d) === 10) {
        return '(' . substr($d, 0, 2) . ') ' . substr($d, 2, 4) . '-' . substr($d, 6);
    }
    return null;
}

function data_valida(string $data): bool
{
    $obj = DateTime::createFromFormat('Y-m-d', $data);
    return $obj && $obj->format('Y-m-d') === $data;
}

function hora_valida(string $hora): bool
{
    $obj = DateTime::createFromFormat('H:i', $hora);
    return $obj && $obj->format('H:i') === $hora;
}

/* ---------- Dados fixos do site ---------- */

function procedimentos(): array
{
    return [
        ['nome' => 'Botox',           'imagem' => 'botox.jpg',           'largura' => 800, 'altura' => 450,
         'descricao' => 'Reduz rugas e linhas de expressão com resultados naturais.'],
        ['nome' => 'Endolifting',     'imagem' => 'endolifting.jpg',     'largura' => 800, 'altura' => 600,
         'descricao' => 'Rejuvenescimento facial sem cirurgia com tecnologia avançada.'],
        ['nome' => 'Limpeza de Pele', 'imagem' => 'limpeza-de-pele.jpg', 'largura' => 550, 'altura' => 315,
         'descricao' => 'Remove impurezas e renova a pele com delicadeza.'],
        ['nome' => 'Injetáveis',      'imagem' => 'injetaveis.jpg',      'largura' => 800, 'altura' => 640,
         'descricao' => 'Tratamentos estéticos com aplicação segura e precisa.'],
    ];
}
