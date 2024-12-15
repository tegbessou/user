<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Domain\Entity;

use EmpireDesAmis\User\Domain\Event\UserCreated;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;
use EmpireDesAmis\User\Domain\ValueObject\UserId;
use TegCorp\SharedKernelBundle\Domain\Entity\EntityDomainEventTrait;
use TegCorp\SharedKernelBundle\Domain\Entity\EntityWithDomainEventInterface;

final class User implements EntityWithDomainEventInterface
{
    use EntityDomainEventTrait;

    public function __construct(
        private UserId $id,
        private UserEmail $email,
    ) {
    }

    public static function create(
        UserId $id,
        UserEmail $email,
    ): self {
        $user = new self(
            $id,
            $email,
        );

        $user::recordEvent(
            new UserCreated(
                $user->id->value(),
                $user->email->value(),
            ),
        );

        return $user;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): UserEmail
    {
        return $this->email;
    }
}
