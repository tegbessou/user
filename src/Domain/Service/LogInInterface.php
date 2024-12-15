<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Domain\Service;

use EmpireDesAmis\User\Domain\ValueObject\UserServiceLoggedIn;

interface LogInInterface
{
    public function logInWithEmail(string $email, string $password): UserServiceLoggedIn;
}
