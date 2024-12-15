<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Domain\Event;

use TegCorp\SharedKernelBundle\Domain\Event\DomainEventInterface;

final class UserCreated implements DomainEventInterface
{
    public function __construct(
        public string $id,
        public string $email,
    ) {
    }
}
