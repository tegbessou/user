<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Application\EventListener;

use EmpireDesAmis\User\Application\Service\MessageBrokerInterface;
use EmpireDesAmis\User\Domain\Event\UserCreated;

final readonly class OnUserCreated
{
    public function __construct(
        private MessageBrokerInterface $messageBrokerService,
    ) {
    }

    public function __invoke(UserCreated $event): void
    {
        $this->messageBrokerService->dispatchUserCreatedMessage(
            $event,
        );
    }
}
