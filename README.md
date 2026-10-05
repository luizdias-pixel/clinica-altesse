# Clínica Altesse

Sistema web da Clínica Altesse (Porto Nacional — TO) para agendar pacientes, cadastrar médicos e consultar a agenda.
Projeto de estudo feito com **PHP, MySQL, HTML, CSS e JavaScript**.

## O que o sistema faz

- **Área pública:** vitrine de procedimentos e formulário de agendamento (sem login).
- **Área administrativa (com login):** painel, lista/edição/exclusão de pacientes, cadastro e lista de médicos, agenda em calendário.

## Estrutura das pastas

```
clinica-altesse/
├── public/                 ← única pasta acessível pelo navegador (DocumentRoot)
│   ├── index.php           painel (exige login)
│   ├── inicio.php          página inicial do paciente
│   ├── login.php  logout.php
│   ├── agenda.php  eventos.php   calendário + dados em JSON
│   ├── pacientes/          cadastrar · listar · editar · excluir
│   ├── medicos/            cadastrar · listar
│   └── assets/             css/ · js/ · img/
├── src/
│   ├── iniciar.php         carregado em toda página (sessão, erros, classes)
│   ├── funcoes.php         e(), validações, CSRF, formatação
│   └── classes/            Conexao · Usuario · Paciente · Medico
├── templates/              cabeçalho, rodapé, menus e lista de procedimentos
├── config/                 config.exemplo.php (modelo) · config.php (local, fora do Git)
├── database/               clinica_altesse.sql · criar_admin.php
├── vitrine/                site estático usado no Netlify
├── netlify.toml · netlify-build.sh
└── .gitignore
```

Por que separar assim? Só o que está em `public/` é entregue ao navegador. As classes, a configuração e o banco ficam fora dele, então ninguém consegue abrir `config.php` digitando a URL.

## Como rodar no seu computador

**Requisitos:** PHP 8.0 ou superior (com a extensão `mysqli`) e MySQL/MariaDB (o XAMPP traz tudo).

1. Coloque a pasta do projeto em `htdocs` (XAMPP) ou onde preferir.
2. Importe o banco: no phpMyAdmin use *Importar* com `database/clinica_altesse.sql`
   (ou no terminal: `mysql -u root -p < database/clinica_altesse.sql`).
3. Copie `config/config.exemplo.php` para `config/config.php` e ajuste usuário/senha do MySQL se precisar.
4. Crie o usuário administrador (a senha é guardada criptografada):
   ```
   php database/criar_admin.php seu@email.com SuaSenhaForte123
   ```
5. Inicie o servidor apontando para a pasta `public`:
   ```
   php -S localhost:8000 -t public
   ```
   e abra http://localhost:8000/login.php

   No XAMPP, também funciona acessando `http://localhost/clinica-altesse/` (ele redireciona para `public/login.php`).

   **Se a página abrir sem estilo (sem cores e fontes):** o endereço do CSS não foi descoberto. Abra o `config/config.php`
   e preencha `url_base` com o caminho até a pasta `public`, por exemplo `'url_base' => '/clinica-altesse/public'`.

> A senha antiga `123456` em texto puro **não funciona mais**. Rode o passo 4 para criar um login novo.

## Segurança aplicada

| Problema | O que foi feito |
|---|---|
| **SQL Injection** | Todas as consultas usam *prepared statements* (`?` + `bind_param`). O que o usuário digita nunca vira parte do SQL. |
| **Senhas em texto puro** | Senha guardada com `password_hash` e conferida com `password_verify`. |
| **XSS** | Tudo que vem do usuário/banco é exibido com a função `e()` (`htmlspecialchars`). |
| **CSRF** | Formulários POST levam um token de sessão, validado no servidor. |
| **Exclusão por link** | Excluir agora pede confirmação e só apaga por POST com token. |
| **Dados sem proteção** | `eventos.php` (agenda) só responde para usuário logado. |
| **Sessão** | `session_regenerate_id` no login, cookie `HttpOnly` e `SameSite`, logout apaga o cookie. |
| **Validação** | CPF (com dígitos verificadores), e-mail, telefone, datas, horas e tamanhos máximos validados no PHP. |
| **Erros expostos** | Em produção (`debug` = `false`) o usuário vê só uma mensagem genérica; o detalhe vai para o log. |

## GitHub

```
git init
git add .
git commit -m "Versão inicial da Clínica Altesse"
git branch -M main
git remote add origin https://github.com/SEU_USUARIO/clinica-altesse.git
git push -u origin main
```

O `.gitignore` já impede que `config/config.php` (com a senha do banco) seja enviado. Confira com `git status` antes do primeiro commit.

## Netlify — leia antes de publicar

**O Netlify hospeda apenas sites estáticos (HTML, CSS, JS). Ele não executa PHP nem tem MySQL.** Se você enviasse o projeto inteiro, o navegador baixaria o código `.php` em vez de executá-lo e login/cadastro não funcionariam.

Por isso o Netlify publica só a **vitrine** (`vitrine/index.html`): a página institucional com os procedimentos. O build (`netlify-build.sh`) monta a pasta `dist` com ela, o CSS e as imagens.

1. No Netlify: *Add new site → Import an existing project* → escolha o repositório do GitHub.
2. As configurações vêm do `netlify.toml` (build `sh netlify-build.sh`, publicação `dist`). Não precisa mudar nada.

### Para o sistema completo funcionar online

É preciso uma hospedagem com **PHP e MySQL**. Opções comuns: InfinityFree, 000webhost/Hostinger, uma hospedagem compartilhada brasileira ou uma VPS.

1. Crie o banco no painel da hospedagem e importe `database/clinica_altesse.sql`.
2. Envie as pastas pelo FTP/gerenciador de arquivos e crie o `config/config.php` com os dados do banco **da hospedagem**, com `'debug' => false`.
3. Faça o servidor apontar para a pasta `public/` (se a hospedagem exigir `public_html`, coloque o conteúdo de `public/` lá e as demais pastas ao lado dela).
4. Crie o administrador com `php database/criar_admin.php` (se não houver terminal, peça ajuda: o script precisa rodar uma vez).
5. Use HTTPS (a maioria das hospedagens oferece gratuitamente).

Depois disso, o botão da vitrine do Netlify pode apontar para o endereço do sistema.
