<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Domain\ValueObject;

final readonly class UserServiceLoggedIn
{
    public function __construct(
        private UserServiceLoggedInToken $token,
    ) {
    }

    public static function create(
        UserServiceLoggedInToken $token,
    ): self {
        return new self(
            $token,
        );
    }

    public function token(): UserServiceLoggedInToken
    {
        return $this->token;
    }
}
