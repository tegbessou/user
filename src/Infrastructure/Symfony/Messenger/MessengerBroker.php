<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\Symfony\Messenger;

use EmpireDesAmis\User\Application\Service\MessageBrokerInterface;
use EmpireDesAmis\User\Domain\Event\UserCreated;
use EmpireDesAmis\User\Infrastructure\Symfony\Messenger\Message\UserCreatedMessage;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessengerBroker implements MessageBrokerInterface
{
    public function __construct(
        private MessageBusInterface $eventBus,
    ) {
    }

    #[\Override]
    public function dispatchUserCreatedMessage(UserCreated $event): void
    {
        $this->eventBus->dispatch(
            UserCreatedMessage::fromEvent($event)
        );
    }
}
