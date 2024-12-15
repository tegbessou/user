<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Domain\ValueObject;

use TegCorp\SharedKernelBundle\Infrastructure\Webmozart\Assert;

final readonly class UserId
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::minLength($value, 36);
        Assert::maxLength($value, 36);
        Assert::uuid($value);

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
}
