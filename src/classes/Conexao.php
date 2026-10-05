<?php
// Cria UMA conexão com o MySQL e reaproveita ela durante a requisição.

class Conexao
{
    private static ?mysqli $conexao = null;

    public static function obter(): mysqli
    {
        if (self::$conexao === null) {
            $c = require RAIZ . '/config/config.php';

            try {
                self::$conexao = new mysqli($c['host'], $c['usuario'], $c['senha'], $c['banco'], $c['porta']);
                self::$conexao->set_charset('utf8mb4');
            } catch (mysqli_sql_exception $erro) {
                // O detalhe sempre vai para o log do servidor
                error_log('Erro de conexão com o banco: ' . $erro->getMessage());

                // Na tela, o motivo real só aparece em modo debug (no seu computador).
                $detalhe = !empty($c['debug']) ? ' Motivo: ' . $erro->getMessage() : '';
                throw new RuntimeException('Não foi possível conectar ao banco de dados.' . $detalhe);
            }
        }

        return self::$conexao;
    }
}
