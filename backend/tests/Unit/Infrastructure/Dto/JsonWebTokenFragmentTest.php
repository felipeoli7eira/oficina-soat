<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Dto;

use App\Infrastructure\Dto\JsonWebTokenFragment;
use PHPUnit\Framework\TestCase;

class JsonWebTokenFragmentTest extends TestCase
{
    public function testConstructor()
    {
        $fragment = new JsonWebTokenFragment(
            sub: 'user-uuid-123',
            iss: 'oficina-soat',
            aud: 'oficina-soat-api',
            iat: 1700000000,
            exp: 1700003600,
            nbf: 1700000000,
        );

        $this->assertInstanceOf(JsonWebTokenFragment::class, $fragment);
        $this->assertEquals('user-uuid-123', $fragment->sub);
        $this->assertEquals('oficina-soat', $fragment->iss);
        $this->assertEquals('oficina-soat-api', $fragment->aud);
        $this->assertEquals(1700000000, $fragment->iat);
        $this->assertEquals(1700003600, $fragment->exp);
        $this->assertEquals(1700000000, $fragment->nbf);
    }

    public function testToAssociativeArray()
    {
        $fragment = new JsonWebTokenFragment(
            sub: 'user-uuid-123',
            iss: 'oficina-soat',
            aud: 'oficina-soat-api',
            iat: 1700000000,
            exp: 1700003600,
            nbf: 1700000000,
        );

        $array = $fragment->toAssociativeArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('sub', $array);
        $this->assertArrayHasKey('iss', $array);
        $this->assertArrayHasKey('aud', $array);
        $this->assertArrayHasKey('iat', $array);
        $this->assertArrayHasKey('exp', $array);
        $this->assertArrayHasKey('nbf', $array);
        $this->assertEquals('user-uuid-123', $array['sub']);
        $this->assertEquals('oficina-soat', $array['iss']);
        $this->assertEquals('oficina-soat-api', $array['aud']);
        $this->assertEquals(1700000000, $array['iat']);
        $this->assertEquals(1700003600, $array['exp']);
        $this->assertEquals(1700000000, $array['nbf']);
    }
}
