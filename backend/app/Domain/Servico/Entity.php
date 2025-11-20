<?php

declare(strict_types=1);

namespace App\Domain\Servico;

use App\Exception\DomainHttpException;
use DateTime;

final class Entity
{
    public const STATUS_DISPONIVEL = true;
    public const STATUS_INDISPONIVEL = false;

    public function __construct(
        public string $uuid,
        public string $nome,
        public float $valor,
        public bool $disponivel,

        public DateTime $criadoEm,
        public ?DateTime $atualizadoEm = null,
        public ?DateTime $deletadoEm = null,
    ) {
        $this->validarEntrada();
    }

    public function validarEntrada(): void
    {
        $this->garantirNomeValido();
        $this->garantirValorValido();
        $this->garantirDisponivelValido();
        // $this->garantirDocumentoValido();
        // $this->garantirFoneValido();
    }

    private function garantirNomeValido(): void
    {
        if (empty(trim($this->nome))) {
            throw new DomainHttpException('O nome é obrigatório', 400);
        }

        if (mb_strlen(trim($this->nome)) < 3) {
            throw new DomainHttpException('O nome deve ter no mínimo 3 caracteres', 400);
        }
    }

    private function garantirValorValido(): void
    {
        if (is_numeric($this->valor) === false) {
            throw new DomainHttpException('O valor deve ser numérico', 400);
        }

        if ($this->valor < 0) {
            throw new DomainHttpException('O valor não pode ser negativo. Caso seja um serviço grátis, informe 0', 400);
        }
    }

    private function garantirDisponivelValido(): void
    {
        if (is_bool($this->disponivel) === false) {
            throw new DomainHttpException('O status disponível deve ser booleano. Quando não informado, é assumido false.', 400);
        }
    }

    public function asArray(): array
    {
        return [
            'uuid'          => $this->uuid,
            'nome'          => $this->nome,
            'valor'         => $this->valor,
            'disponivel'    => $this->disponivel,

            'criado_em'     => $this->criadoEm->format('Y-m-d H:i:s'),
            'deletado_em'   => $this->deletadoEm ? $this->deletadoEm->format('Y-m-d H:i:s') : null,
            'atualizado_em' => $this->atualizadoEm ? $this->atualizadoEm->format('Y-m-d H:i:s') : null,
        ];
    }

    public function toExternal(): array
    {
        return [
            'uuid'       => $this->uuid,
            'nome'       => $this->nome,
            'valor'      => $this->valor / 100, // converte de volta em reais
            'valor_gui'  => number_format($this->valor / 100, 2, ',', '.'), // valor formatado para uma GUI (grafic user interface)
            'disponivel' => $this->disponivel,
            'criado_em'  => $this->criadoEm->format('d/m/Y H:i:s'),
        ];
    }

    public function converteValorEmCentavos(): void
    {
        $this->valor = round($this->valor * 100);
    }

    public function delete(): void
    {
        $this->atualizadoEm = new DateTime();
        $this->deletadoEm   = new DateTime();
    }

    public function update(array $novosDados): void
    {
        if (isset($novosDados['nome'])) {
            $this->nome = $novosDados['nome'];
        }

        if (isset($novosDados['valor'])) {
            $this->valor = $novosDados['valor'];
        }

        if (isset($novosDados['disponivel'])) {
            $this->disponivel = $novosDados['disponivel'];
        }

        $this->atualizadoEm = new DateTime();

        $this->validarEntrada();
    }
}
