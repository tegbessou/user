<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\EmpireDesAmis\Security\LogIn;

use EmpireDesAmis\SecurityAuthenticatorBundle\Firebase\Security\LogIn\LogInFirebaseInterface;
use EmpireDesAmis\User\Domain\Service\LogInInterface;
use EmpireDesAmis\User\Domain\ValueObject\UserServiceLoggedIn;
use EmpireDesAmis\User\Domain\ValueObject\UserServiceLoggedInToken;

final readonly class LogInEmpireDesAmis implements LogInInterface
{
    public function __construct(
        private LogInFirebaseInterface $auth,
    ) {
    }

    #[\Override]
    public function logInWithEmail(string $email, string $password): UserServiceLoggedIn
    {
        $token = $this->auth->logInWithEmail(
            $email,
            $password,
        );

        return new UserServiceLoggedIn(
            UserServiceLoggedInToken::fromString($token->token),
        );
    }
}
