<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Domain\ValueObject;

use TegCorp\SharedKernelBundle\Infrastructure\Webmozart\Assert;

final readonly class UserServiceLoggedInToken
{
    private string $token;

    public function __construct(
        string $token,
    ) {
        Assert::string($token);

        $this->token = $token;
    }

    public static function fromString(string $token): self
    {
        return new self($token);
    }

    public function token(): string
    {
        return $this->token;
    }
}
