<?php
// Este arquivo é carregado no começo de TODAS as páginas.
// Ele prepara configurações, erros, classes, funções e sessão.

define('RAIZ', dirname(__DIR__));

// 1) Configurações
$arquivoConfig = RAIZ . '/config/config.php';
if (!file_exists($arquivoConfig)) {
    http_response_code(500);
    die('Arquivo config/config.php não encontrado. Copie config/config.exemplo.php para config/config.php.');
}
$config = require $arquivoConfig;

// 2) Erros: detalhes só aparecem em modo debug
error_reporting(E_ALL);
ini_set('display_errors', !empty($config['debug']) ? '1' : '0');

set_exception_handler(function (Throwable $erro) use ($config) {
    error_log((string) $erro); // registra no log do servidor
    http_response_code(500);
    if (!empty($config['debug'])) {
        echo '<pre>' . htmlspecialchars((string) $erro, ENT_QUOTES, 'UTF-8') . '</pre>';
    } else {
        echo 'Ocorreu um erro inesperado. Tente novamente mais tarde.';
    }
});

// 3) O mysqli passa a lançar exceções quando o SQL falha (permite usar try/catch)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// 4) Carrega as classes sozinho: new Paciente() procura src/classes/Paciente.php
spl_autoload_register(function (string $classe) {
    if (preg_match('/^[A-Za-z]+$/', $classe)) {
        $arquivo = RAIZ . '/src/classes/' . $classe . '.php';
        if (file_exists($arquivo)) {
            require_once $arquivo;
        }
    }
});

require_once RAIZ . '/src/funcoes.php';

// 5) Sessão com cookie mais seguro
$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $https,
    'httponly' => true,   // JavaScript não consegue ler o cookie da sessão
    'samesite' => 'Lax',  // o cookie não é enviado em requisições vindas de outros sites
]);
session_start();
