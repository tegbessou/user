<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\Symfony\Messenger\Message;

use EmpireDesAmis\User\Domain\Event\UserCreated;

final readonly class UserCreatedMessage
{
    public function __construct(
        public string $email,
        public string $fullName,
    ) {
    }

    public static function fromEvent(
        UserCreated $event,
    ): self {
        return new self(
            $event->email,
            'Hugues Gobet',
        );
    }
}
