<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Domain\ValueObject;

use TegCorp\SharedKernelBundle\Infrastructure\Webmozart\Assert;

final readonly class UserEmail
{
    private string $value;

    public function __construct(
        string $value,
    ) {
        Assert::lengthBetween($value, 1, 255);
        Assert::email($value);

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(UserEmail $email): bool
    {
        return $this->value === $email->value;
    }
}
