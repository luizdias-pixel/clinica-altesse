<?php
// Entrega os agendamentos em JSON para o calendário (agenda.php).
require_once __DIR__ . '/../src/iniciar.php';

header('Content-Type: application/json; charset=utf-8');

// Esses dados são de pacientes: só quem está logado pode ver
if (!usuario_logado()) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autorizado']);
    exit;
}

$pacientes = new Paciente(Conexao::obter());
$eventos   = [];

foreach ($pacientes->listarAgendados() as $dados) {
    $titulo = $dados['nome'];
    if (!empty($dados['procedimento'])) {
        $titulo .= ' - ' . $dados['procedimento'];
    }

    // Se não tiver hora, manda só a data (evento de dia inteiro)
    $inicio = $dados['data_agendamento'];
    if (!empty($dados['hora_agendamento'])) {
        $inicio .= 'T' . $dados['hora_agendamento'];
    }

    $eventos[] = ['title' => $titulo, 'start' => $inicio];
}

echo json_encode($eventos, JSON_UNESCAPED_UNICODE);
