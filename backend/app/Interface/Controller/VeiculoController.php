<?php

declare(strict_types=1);

namespace App\Interface\Controller;

use App\Application\Veiculo\CreateUseCase;
use App\Application\Veiculo\ReadOneByUuidUseCase;

use App\Application\Veiculo\ReadUseCase;
use App\Application\Veiculo\UpdateUseCase;
// use App\Application\Veiculo\DeleteUseCase;

use App\Domain\Veiculo\RepositoryContract as Repository;
use App\Domain\Usuario\RepositoryContract as UsuarioRepository;
use App\Domain\Cliente\RepositoryContract as ClienteRepository;

use App\Exception\DomainHttpException;
use App\Interface\Gateway\ClienteGateway;
use App\Interface\Gateway\VeiculoGateway;
use App\Interface\Gateway\UsuarioGateway;
use RuntimeException;

class VeiculoController
{
    private string $authenticatedUserUuid = '';

    public function __construct(
        public readonly Repository $repo,
        public readonly UsuarioRepository $usuarioRepo,
        public readonly ClienteRepository $clienteRepo,
    ) {}

    public function authenticatedUser(string $uuid): self
    {
        $this->authenticatedUserUuid = $uuid;

        return $this;
    }

    /**
     * @param array $readParams Filtros, ordenações, paginação, etc.
     * @return array
     */
    public function read(array $readParams = []): array
    {
        $gateway = new VeiculoGateway($this->repo);
        $useCase = new ReadUseCase($gateway);

        return $useCase->handle($readParams);
    }

    public function readOneByUuid(string $uuid): array
    {
        $gateway = new VeiculoGateway($this->repo);

        $useCase = new ReadOneByUuidUseCase($gateway);

        return $useCase->handle($uuid);
    }

    public function create(string $marca, string $modelo, string $placa, int $ano, string $clienteDonoUuid)
    {
        if (empty(trim($this->authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        $gateway = new VeiculoGateway($this->repo);
        $usuarioGateway = new UsuarioGateway($this->usuarioRepo);
        $clienteGateway = new ClienteGateway($this->clienteRepo);

        $useCase = new CreateUseCase(
            $marca,
            $modelo,
            $placa,
            $ano,
            $clienteDonoUuid
        );

        $useCase->useGateway($gateway);
        $useCase->useUsuarioGateway($usuarioGateway);
        $useCase->useClienteGateway($clienteGateway);

        return $useCase->handle($this->authenticatedUserUuid);
    }

    // public function delete(string $uuid): bool
    // {
    //     if ($this->repo instanceof Repository === false) {
    //         throw new RuntimeException('Fonte de dados não definida');
    //     }

    //     if ($this->usuarioRepo instanceof UsuarioRepository === false) {
    //         throw new RuntimeException('Fonte de dados não definida');
    //     }

    //     if (empty(trim($this->authenticatedUserUuid))) {
    //         throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
    //     }

    //     $gateway = new ClienteGateway($this->repo);
    //     $usuarioGateway = new UsuarioGateway($this->usuarioRepo);

    //     $useCase = new DeleteUseCase($uuid);
    //     $useCase->useGateway($gateway)->useUsuarioGateway($usuarioGateway);

    //     return $useCase->handle($this->authenticatedUserUuid);
    // }

    public function update(string $uuid, array $novosDados): array
    {
        if ($this->repo instanceof Repository === false) {
            throw new RuntimeException('Fonte de dados não definida');
        }

        if (empty(trim($this->authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        $gateway = new VeiculoGateway($this->repo);
        $usuarioGateway = new UsuarioGateway($this->usuarioRepo);

        $useCase = new UpdateUseCase($uuid, $novosDados);
        $useCase->useGateway($gateway)->useUsuarioGateway($usuarioGateway);

        $res = $useCase->handle($this->authenticatedUserUuid);

        return $res;
    }
}
