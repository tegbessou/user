<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Tests\AdapterTest\DrivingTest\Infrastructure\Symfony\Security\Authenticator;

use EmpireDesAmis\User\Tests\Shared\ApiTestCase;

final class AuthenticatorFirebaseTest extends ApiTestCase
{
    public function testAuthenticateWithApple(): void
    {
        $this->get('/api/users/hugues.gobet@gmail.com');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testAuthenticateWithGoogle(): void
    {
        $this->get('/api/users/hugues.gobet@gmail.com', [], 'google');

        $this->assertResponseStatusCodeSame(200);
    }
}
