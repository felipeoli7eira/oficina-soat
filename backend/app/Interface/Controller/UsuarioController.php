<?php

declare(strict_types=1);

namespace App\Interface\Controller;

use App\Application\Usuario\CreateUseCase;
use App\Application\Usuario\ReadUseCase;
use App\Application\Usuario\ReadOneByUuidUseCase;

use App\Domain\Usuario\RepositoryContract as UsuarioRepository;

use App\Interface\Gateway\UsuarioGateway;

use RuntimeException;

class UsuarioController
{
    public function __construct(public readonly UsuarioRepository $repo) {}

    /**
     * @param array $readParams Filtros, ordenações, paginação, etc.
     * @return array
    */
    public function read(array $readParams = []): array
    {
        if ($this->repo instanceof UsuarioRepository === false) {
            throw new RuntimeException('Fonte de dados não definida');
        }

        $gateway = new UsuarioGateway($this->repo);

        $useCase = new ReadUseCase($gateway);

        return $useCase->handle($readParams);
    }

    public function readOneByUuid(string $uuid): array
    {
        if ($this->repo instanceof UsuarioRepository === false) {
            throw new RuntimeException('Fonte de dados não definida');
        }

        $gateway = new UsuarioGateway($this->repo);

        $useCase = new ReadOneByUuidUseCase($gateway);

        return $useCase->handle($uuid);
    }

    public function create(string $nome, string $email, string $senhaAcessoSistema, string $perfil, bool $ativo = true)
    {
        if ($this->repo instanceof UsuarioRepository === false) {
            throw new RuntimeException('Fonte de dados não definida');
        }

        $gateway = new UsuarioGateway($this->repo);

        $useCase = new CreateUseCase(
            $nome,
            $email,
            $senhaAcessoSistema,
            $perfil,
            $ativo,
        );

        $useCase->useGateway($gateway);

        return $useCase->handle();
    }
}
