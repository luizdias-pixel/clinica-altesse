<?php
// Cria (ou troca a senha de) um usuário administrador, guardando a senha CRIPTOGRAFADA.
//
// Uso no terminal, a partir da pasta do projeto:
//   php database/criar_admin.php adm@altesse.com MinhaSenhaForte123
//
// Se o e-mail já existir no banco, a senha dele é substituída.

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado pelo terminal.');
}

require_once __DIR__ . '/../src/iniciar.php';

if ($argc !== 3) {
    exit("Uso: php database/criar_admin.php email senha\n");
}

[, $email, $senha] = $argv;

if (!email_valido($email)) {
    exit("E-mail inválido.\n");
}
if (strlen($senha) < 8) {
    exit("A senha precisa ter pelo menos 8 caracteres.\n");
}

$usuarios = new Usuario(Conexao::obter());
$usuarios->salvarComSenha($email, $senha);

echo "Usuário {$email} salvo com sucesso.\n";
