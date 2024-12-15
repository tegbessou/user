<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Tests\AdapterTest\DrivingTest\Infrastructure\ApiPlatform\State\Provider;

use EmpireDesAmis\User\Tests\Shared\ApiTestCase;

final class GetUserProviderTest extends ApiTestCase
{
    public function testGetCurrent(): void
    {
        $this->get('/api/users/hugues.gobet@gmail.com');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            'email' => 'hugues.gobet@gmail.com',
            'isCurrent' => true,
        ]);
    }

    public function testGetNotCurrent(): void
    {
        $this->get('/api/users/root@gmail.com');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            'email' => 'root@gmail.com',
            'isCurrent' => false,
        ]);
    }

    public function testGetNotFound(): void
    {
        $this->get('/api/users/pedro@gmail.com');

        $this->assertResponseStatusCodeSame(404);
    }
}
