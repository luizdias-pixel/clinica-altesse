<?php
// Tudo que envolve a tabela "medicos".

class Medico
{
    private mysqli $con;

    public function __construct(mysqli $con)
    {
        $this->con = $con;
    }

    public function listar(): array
    {
        $sql = 'SELECT id, nome, crm, especialidade, telefone, email FROM medicos ORDER BY nome';
        return $this->con->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function crmExiste(string $crm): bool
    {
        $stmt = $this->con->prepare('SELECT 1 FROM medicos WHERE crm = ?');
        $stmt->bind_param('s', $crm);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // $d precisa ter: nome, crm, especialidade, telefone, email
    public function cadastrar(array $d): void
    {
        $sql = 'INSERT INTO medicos (nome, crm, especialidade, telefone, email) VALUES (?, ?, ?, ?, ?)';
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('sssss', $d['nome'], $d['crm'], $d['especialidade'], $d['telefone'], $d['email']);
        $stmt->execute();
    }
}
