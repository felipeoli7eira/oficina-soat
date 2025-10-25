<?php

declare(strict_types=1);

namespace App\Interface\Controller;

use App\Application\Cliente\ReadUseCase;
use App\Application\Cliente\CreateUseCase;
use App\Application\Cliente\ReadOneByUuidUseCase;
use App\Application\Cliente\DeleteUseCase;
use App\Application\Cliente\UpdateUseCase;

use App\Domain\Cliente\RepositoryContract as Repository;
use App\Domain\Usuario\RepositoryContract as UsuarioRepository;
use App\Exception\DomainHttpException;

use App\Interface\Gateway\ClienteGateway;
use App\Interface\Gateway\UsuarioGateway;
use RuntimeException;

class ClienteController
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
        $gateway = new ClienteGateway($this->repo);
        $useCase = new ReadUseCase($gateway);

        return $useCase->handle($readParams);
    }

    public function readOneByUuid(string $uuid): array
    {
        $gateway = new ClienteGateway($this->repo);

        $useCase = new ReadOneByUuidUseCase($gateway);

        return $useCase->handle($uuid);
    }

    // public function createUnauthenticated(string $nome, string $email, string $senhaAcessoSistema, string $perfil, bool $ativo = true)
    // {
    //     if ($this->repo instanceof UsuarioRepository === false) {
    //         throw new RuntimeException('Fonte de dados não definida');
    //     }

    //     $gateway = new UsuarioGateway($this->repo);

    //     $useCase = new CreateUnauthenticatedUseCase(
    //         $nome,
    //         $email,
    //         $senhaAcessoSistema,
    //         $perfil,
    //         $ativo,
    //     );
    //     $useCase->useGateway($gateway);

    //     return $useCase->handle();
    // }

    public function create(string $nome, string $email, string $documento, string $fone)
    {
        if (empty(trim($this->authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        $gateway = new ClienteGateway($this->repo);
        $usuarioGateway = new UsuarioGateway($this->usuarioRepo);

        $useCase = new CreateUseCase(
            $nome,
            $email,
            $documento,
            $fone
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
            throw new RuntimeException('Fonte de dados não definida');
        }

        if (empty(trim($this->authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        $gateway = new ClienteGateway($this->repo);
        $usuarioGateway = new UsuarioGateway($this->usuarioRepo);

        $useCase = new DeleteUseCase($uuid);
        $useCase->useGateway($gateway)->useUsuarioGateway($usuarioGateway);

        return $useCase->handle($this->authenticatedUserUuid);
    }

    public function update(string $uuid, array $novosDados): array
    {
        if ($this->repo instanceof Repository === false) {
            throw new RuntimeException('Fonte de dados não definida');
        }

        if (empty(trim($this->authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        $gateway = new ClienteGateway($this->repo);
        $usuarioGateway = new UsuarioGateway($this->usuarioRepo);


        $useCase = new UpdateUseCase($uuid, $novosDados);
        $useCase->useGateway($gateway)->useUsuarioGateway($usuarioGateway);

        $res = $useCase->handle($this->authenticatedUserUuid);

        return $res;
    }
}
