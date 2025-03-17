<?php

declare(strict_types=1);

namespace AdapterTest\DrivingTest\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use EmpireDesAmis\User\Tests\Shared\RefreshDatabase;

final class UpProviderTest extends ApiTestCase
{
    use RefreshDatabase;

    public function testGet(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/up');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }
}
