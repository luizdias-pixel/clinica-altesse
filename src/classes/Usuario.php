<?php
// Tudo que envolve a tabela "usuarios" (quem pode entrar no painel).

class Usuario
{
    private mysqli $con;

    public function __construct(mysqli $con)
    {
        $this->con = $con;
    }

    // Devolve os dados do usuário se e-mail e senha estiverem certos; senão, null.
    public function autenticar(string $email, string $senha): ?array
    {
        $stmt = $this->con->prepare('SELECT id_usuario, email, senha FROM usuarios WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        // password_verify compara a senha digitada com o hash guardado no banco
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            unset($usuario['senha']); // a senha não precisa sair daqui
            return $usuario;
        }
        return null;
    }

    // Cria o usuário ou troca a senha dele. Usado pelo script database/criar_admin.php
    public function salvarComSenha(string $email, string $senha): void
    {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $this->con->prepare('SELECT id_usuario FROM usuarios WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $existe = $stmt->get_result()->fetch_assoc();

        if ($existe) {
            $stmt = $this->con->prepare('UPDATE usuarios SET senha = ? WHERE email = ?');
            $stmt->bind_param('ss', $hash, $email);
        } else {
            $stmt = $this->con->prepare('INSERT INTO usuarios (email, senha) VALUES (?, ?)');
            $stmt->bind_param('ss', $email, $hash);
        }
        $stmt->execute();
    }
}
