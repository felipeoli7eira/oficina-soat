<?php

declare(strict_types=1);

namespace App\Interface\Controller;

use App\Application\Cliente\ReadUseCase;
// use App\Application\Usuario\CreateUseCase;
// use App\Application\Usuario\ReadOneByUuidUseCase;
// use App\Application\Usuario\DeleteUseCase;
// use App\Application\Usuario\UpdateUseCase;
// use App\Application\Usuario\PasswordVerifyUseCase;
// use App\Application\Usuario\AuthenticateUseCase;
// use App\Application\Usuario\CreateUnauthenticatedUseCase;

use App\Infrastructure\Service\JsonWebTokenHandler\JsonWebTokenHandlerContract;
use App\Domain\Cliente\RepositoryContract as ClienteRepository;
use App\Exception\DomainHttpException;

use App\Interface\Gateway\ClienteGateway;

use RuntimeException;

class ClienteController
{
    // private string $authenticatedUserUuid = '';

    public function __construct(public readonly ClienteRepository $repo) {}

    // public function authenticatedUser(string $authenticatedUserUuid): self
    // {
    //     $this->authenticatedUserUuid = $authenticatedUserUuid;

    //     return $this;
    // }

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

    // public function readOneByUuid(string $uuid): array
    // {
    //     if ($this->repo instanceof UsuarioRepository === false) {
    //         throw new RuntimeException('Fonte de dados não definida');
    //     }

    //     $gateway = new UsuarioGateway($this->repo);

    //     $useCase = new ReadOneByUuidUseCase($gateway);

    //     return $useCase->handle($uuid);
    // }

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

    // public function create(string $nome, string $email, string $senhaAcessoSistema, string $perfil, bool $ativo = true)
    // {
    //     if ($this->repo instanceof UsuarioRepository === false) {
    //         throw new RuntimeException('Fonte de dados não definida');
    //     }

    //     if (empty(trim($this->authenticatedUserUuid))) {
    //         throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
    //     }

    //     $gateway = new UsuarioGateway($this->repo);

    //     $useCase = new CreateUseCase(
    //         $nome,
    //         $email,
    //         $senhaAcessoSistema,
    //         $perfil,
    //         $ativo,
    //     );
    //     $useCase->useGateway($gateway);

    //     return $useCase->handle($this->authenticatedUserUuid);
    // }

    // public function delete(string $uuid): bool
    // {
    //     if ($this->repo instanceof UsuarioRepository === false) {
    //         throw new RuntimeException('Fonte de dados não definida');
    //     }

    //     if (empty(trim($this->authenticatedUserUuid))) {
    //         throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
    //     }

    //     $gateway = new UsuarioGateway($this->repo);

    //     $useCase = new DeleteUseCase($uuid);
    //     $useCase->useGateway($gateway);

    //     return $useCase->handle($this->authenticatedUserUuid);
    // }

    // public function update(string $uuid, array $novosDados): array
    // {
    //     if ($this->repo instanceof UsuarioRepository === false) {
    //         throw new RuntimeException('Fonte de dados não definida');
    //     }

    //     if (empty(trim($this->authenticatedUserUuid))) {
    //         throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
    //     }

    //     $gateway = new UsuarioGateway($this->repo);

    //     $useCase = new UpdateUseCase($uuid, $novosDados);
    //     $useCase->useGateway($gateway);

    //     $res = $useCase->handle($this->authenticatedUserUuid);

    //     return $res;
    // }

    // public function getAuthJwt(string $email, string $senhaAcessoSistema, JsonWebTokenHandlerContract $tokenHandler): string
    // {
    //     if ($this->repo instanceof UsuarioRepository === false) {
    //         throw new RuntimeException('Fonte de dados não definida');
    //     }

    //     $gateway = new UsuarioGateway($this->repo);

    //     $passwordVerifyUseCase = new PasswordVerifyUseCase($email, $senhaAcessoSistema);
    //     $passwordVerifyUseCase->useGateway($gateway);

    //     $dadosEntity = $passwordVerifyUseCase->handle();

    //     $generateTokenUseCase = new AuthenticateUseCase($dadosEntity, $tokenHandler);
    //     $token = $generateTokenUseCase->handle();

    //     return $token;
    // }
}
