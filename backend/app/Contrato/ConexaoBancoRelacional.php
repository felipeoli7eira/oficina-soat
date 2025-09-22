<?php

declare(strict_types=1);

namespace App\Contrato;

interface ConexaoBancoRelacional
{
    public function obterConexao(): \PDO;
    public function fecharConexao(): void;
    public function executarComando(string $comando): void;
    public function executarComandoComRetorno(string $comando): array;
    public function executarComandoComRetornoUnico(string $comando): array;
    
}
