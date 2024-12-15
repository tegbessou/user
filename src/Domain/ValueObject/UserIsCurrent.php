<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Domain\ValueObject;

final readonly class UserIsCurrent
{
    public function __construct(
        private UserEmail $email,
        private bool $current,
    ) {
    }

    public static function create(
        UserEmail $email,
        bool $current,
    ): self {
        return new self(
            $email,
            $current,
        );
    }

    public function email(): UserEmail
    {
        return $this->email;
    }

    public function isCurrent(): bool
    {
        return $this->current;
    }
}
