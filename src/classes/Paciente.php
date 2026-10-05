<?php
// Tudo que envolve a tabela "pacientes": INSERT, SELECT, UPDATE e DELETE.
// Todas as consultas usam "?" no lugar dos valores (prepared statements),
// por isso o que o usuário digitar nunca vira parte do comando SQL.

class Paciente
{
    private mysqli $con;

    public function __construct(mysqli $con)
    {
        $this->con = $con;
    }

    public function listar(): array
    {
        $sql = 'SELECT id_paciente, nome, cpf, telefone, procedimento, possui_alergia
                FROM pacientes ORDER BY nome';
        return $this->con->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->con->prepare('SELECT * FROM pacientes WHERE id_paciente = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function cpfExiste(string $cpf): bool
    {
        $stmt = $this->con->prepare('SELECT 1 FROM pacientes WHERE cpf = ?');
        $stmt->bind_param('s', $cpf);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // $d precisa ter: nome, email, cpf, telefone, endereco, procedimento, possui_alergia
    public function cadastrar(array $d): void
    {
        $sql = 'INSERT INTO pacientes (nome, email, cpf, telefone, endereco, procedimento, possui_alergia)
                VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param(
            'sssssss',
            $d['nome'], $d['email'], $d['cpf'], $d['telefone'],
            $d['endereco'], $d['procedimento'], $d['possui_alergia']
        );
        $stmt->execute();
    }

    // $d precisa ter: nome, telefone, procedimento, data_agendamento, hora_agendamento (os dois últimos podem ser null)
    public function atualizar(int $id, array $d): void
    {
        $sql = 'UPDATE pacientes
                SET nome = ?, telefone = ?, procedimento = ?, data_agendamento = ?, hora_agendamento = ?
                WHERE id_paciente = ?';
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param(
            'sssssi',
            $d['nome'], $d['telefone'], $d['procedimento'],
            $d['data_agendamento'], $d['hora_agendamento'], $id
        );
        $stmt->execute();
    }

    // Devolve true se algum paciente foi realmente apagado
    public function excluir(int $id): bool
    {
        $stmt = $this->con->prepare('DELETE FROM pacientes WHERE id_paciente = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // Pacientes que já têm data de agendamento (usado pela agenda)
    public function listarAgendados(): array
    {
        $sql = 'SELECT nome, procedimento, data_agendamento, hora_agendamento
                FROM pacientes WHERE data_agendamento IS NOT NULL';
        return $this->con->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}
