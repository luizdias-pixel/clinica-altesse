<?php
// Copie este arquivo para config/config.php e ajuste os valores.
// O config.php NÃO vai para o GitHub (está no .gitignore), pois guarda a senha do banco.

return [
    'host'    => 'localhost',
    'porta'   => 3306,
    'usuario' => 'root',   // usuário do MySQL (no XAMPP padrão é root)
    'senha'   => '',       // senha do MySQL (no XAMPP padrão fica vazia)
    'banco'   => 'clinica_altesse',

    // true = mostra os erros detalhados na tela (use só no seu computador).
    // false = mostra uma mensagem genérica (use quando o site estiver online).
    'debug'   => true,

    // Normalmente pode deixar null: o sistema descobre sozinho a pasta onde está.
    // Só preencha se os links ou o CSS não carregarem. Exemplo: '/clinica-altesse/public'
    'url_base' => null,
];
