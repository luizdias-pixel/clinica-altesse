<?php
// Cabeçalho comum a todas as páginas. Variáveis que a página pode definir antes de incluir:
//   $titulo       título da aba do navegador
//   $menu         'admin' (padrão), 'publico' ou 'nenhum'
//   $classe_body  classe CSS extra no <body>
//   $head_extra   HTML extra dentro do <head> (ex.: scripts de bibliotecas)
$titulo      = $titulo ?? 'Clínica Altesse';
$menu        = $menu ?? 'admin';
$classe_body = $classe_body ?? '';
$head_extra  = $head_extra ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> — Clínica Altesse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Lato:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('assets/css/estilo.css')) ?>">
    <?= $head_extra ?>
</head>

<body class="<?= e($classe_body) ?>">

    <?php
    if ($menu === 'admin') {
        require RAIZ . '/templates/menu_admin.php';
    } elseif ($menu === 'publico') {
        require RAIZ . '/templates/menu_publico.php';
    }
    ?>

    <main class="conteudo">
