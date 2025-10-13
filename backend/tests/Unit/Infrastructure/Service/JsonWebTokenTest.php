<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service;

use Tests\TestCase;
use App\Infrastructure\Service\JsonWebToken;
use App\Infrastructure\Dto\JsonWebTokenFragment;
use RuntimeException;

class JsonWebTokenTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Garante que JWT_SECRET está configurado
        putenv('JWT_SECRET=test-secret-key-for-testing-purposes-only');
    }

    public function testConstructorComJwtSecretConfigurado()
    {
        $service = new JsonWebToken();
        $this->assertInstanceOf(JsonWebToken::class, $service);
    }

    public function testGenerate()
    {
        $service = new JsonWebToken();

        $claims = [
            'sub' => 'user-uuid-123',
            'name' => 'Test User',
        ];

        $token = $service->generate($claims);

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        // JWT tem 3 partes separadas por ponto
        $this->assertCount(3, explode('.', $token));
    }

    public function testValidateComTokenValido()
    {
        $service = new JsonWebToken();

        $claims = [
            'sub' => 'user-uuid-123',
            'name' => 'Test User',
        ];

        $token = $service->generate($claims);
        $fragment = $service->validate($token);

        $this->assertInstanceOf(JsonWebTokenFragment::class, $fragment);
        $this->assertEquals('user-uuid-123', $fragment->sub);
        $this->assertIsInt($fragment->iat);
        $this->assertIsInt($fragment->exp);
        $this->assertIsInt($fragment->nbf);
    }

    public function testValidateComTokenInvalido()
    {
        $service = new JsonWebToken();

        $tokenInvalido = 'token.invalido.aqui';
        $fragment = $service->validate($tokenInvalido);

        $this->assertNull($fragment);
    }

    public function testValidateComTokenVazio()
    {
        $service = new JsonWebToken();

        $fragment = $service->validate('');

        $this->assertNull($fragment);
    }

    public function testRefreshComTokenValido()
    {
        $service = new JsonWebToken();

        $claims = [
            'sub' => 'user-uuid-123',
            'name' => 'Test User',
        ];

        $token = $service->generate($claims);

        // Aguarda 1 segundo para garantir que o novo token terá timestamp diferente
        sleep(1);

        $newToken = $service->refresh($token);

        $this->assertIsString($newToken);
        $this->assertNotEmpty($newToken);
        $this->assertNotEquals($token, $newToken);

        // Valida que o novo token contém os mesmos claims principais
        $fragment = $service->validate($newToken);
        $this->assertEquals('user-uuid-123', $fragment->sub);
    }

    public function testRefreshComTokenInvalidoLancaExcecao()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Token inválido');

        $service = new JsonWebToken();
        $service->refresh('token.invalido.aqui');
    }

    public function testInvalidate()
    {
        $service = new JsonWebToken();

        $claims = [
            'sub' => 'user-uuid-123',
        ];

        $token = $service->generate($claims);

        // invalidate() não faz nada atualmente (método vazio)
        // Testa apenas que não lança exceção
        $service->invalidate($token);

        // Se chegou aqui, o método foi executado sem erros
        $this->assertTrue(true);
    }

    public function testTokenExpirationStructure()
    {
        $service = new JsonWebToken();

        $claims = [
            'sub' => 'user-uuid-123',
        ];

        $token = $service->generate($claims);
        $fragment = $service->validate($token);

        // Verifica que exp é maior que iat
        $this->assertGreaterThan($fragment->iat, $fragment->exp);

        // Verifica que o token expira em aproximadamente 24h (86400 segundos)
        $diff = $fragment->exp - $fragment->iat;
        $this->assertGreaterThanOrEqual(86390, $diff); // Permite pequena margem
        $this->assertLessThanOrEqual(86410, $diff);
    }

    public function testTokenContainsIssuerAndAudience()
    {
        $service = new JsonWebToken();

        $claims = [
            'sub' => 'user-uuid-123',
        ];

        $token = $service->generate($claims);
        $fragment = $service->validate($token);

        $this->assertIsString($fragment->iss);
        $this->assertIsString($fragment->aud);
        $this->assertNotEmpty($fragment->iss);
        $this->assertNotEmpty($fragment->aud);
    }
}
