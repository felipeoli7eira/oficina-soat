<?php

declare(strict_types=1);

namespace App\Interface\Controller;

use App\Application\Servico\ReadUseCase;
use App\Application\Servico\CreateUseCase;
use App\Application\Servico\ReadOneByUuidUseCase;
use App\Application\Servico\DeleteUseCase;
use App\Application\Servico\UpdateUseCase;

use App\Domain\Servico\RepositoryContract as Repository;
use App\Domain\Usuario\RepositoryContract as UsuarioRepository;
use App\Exception\DomainHttpException;

use App\Interface\Gateway\ServicoGateway;
use App\Interface\Gateway\UsuarioGateway;
use RuntimeException;

class ServicoController
{
    private string $authenticatedUserUuid = '';

    public function __construct(
        public readonly Repository $repo,
        public readonly UsuarioRepository $usuarioRepo,
    ) {}

    public function authenticatedUser(string $authenticatedUserUuid): self
    {
        $this->authenticatedUserUuid = $authenticatedUserUuid;

        return $this;
    }

    /**
     * @param array $readParams Filtros, ordenações, paginação, etc.
     * @return array
     */
    public function read(array $readParams = []): array
    {
        $gateway = new ServicoGateway($this->repo);
        $useCase = new ReadUseCase($gateway);

        return $useCase->handle($readParams);

        return array();
    }

    public function readOneByUuid(string $uuid): array
    {
        $gateway = new ServicoGateway($this->repo);

        $useCase = new ReadOneByUuidUseCase($gateway);

        return $useCase->handle($uuid);
    }

    public function create(string $nome, float $valor, bool $statusDisponivel = false)
    {
        $this->validateAuthenticatedUser();

        $gateway = new ServicoGateway($this->repo);
        $usuarioGateway = new UsuarioGateway($this->usuarioRepo);

        $useCase = new CreateUseCase(
            $nome,
            $valor,
            $statusDisponivel
        );
        $useCase->useGateway($gateway)->useUsuarioGateway($usuarioGateway);

        return $useCase->handle($this->authenticatedUserUuid);
    }

    public function delete(string $uuid): bool
    {
        if ($this->repo instanceof Repository === false) {
            throw new RuntimeException('Fonte de dados não definida');
        }

        if ($this->usuarioRepo instanceof UsuarioRepository === false) {
            throw new RuntimeException('Fonte de dados de usuário não definida');
        }

        if (empty(trim($this->authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        $gateway = new ServicoGateway($this->repo);
        $usuarioGateway = new UsuarioGateway($this->usuarioRepo);

        $useCase = new DeleteUseCase($uuid);
        $useCase->useGateway($gateway)->useUsuarioGateway($usuarioGateway);

        return $useCase->handle($this->authenticatedUserUuid);
        return true;
    }

    public function update(string $uuid, array $novosDados): array
    {
        if ($this->repo instanceof Repository === false) {
            throw new RuntimeException('Fonte de dados não definida');
        }

        if (empty(trim($this->authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        $gateway = new ServicoGateway($this->repo);
        $usuarioGateway = new UsuarioGateway($this->usuarioRepo);

        $useCase = new UpdateUseCase($uuid, $novosDados);
        $useCase->useGateway($gateway)->useUsuarioGateway($usuarioGateway);

        $res = $useCase->handle($this->authenticatedUserUuid);

        return $res;
    }

    private function validateAuthenticatedUser(): void
    {
        if (empty(trim($this->authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }
    }
}
