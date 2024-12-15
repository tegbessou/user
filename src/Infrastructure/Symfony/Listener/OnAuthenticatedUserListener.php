<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\Symfony\Listener;

use EmpireDesAmis\SecurityAuthenticatorBundle\Event\UserAuthenticatedEvent;
use EmpireDesAmis\User\Application\Command\CreateUserCommand;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use TegCorp\SharedKernelBundle\Application\Command\CommandBusInterface;
use TegCorp\SharedKernelBundle\Infrastructure\Webmozart\Assert;

#[AsEventListener]
final readonly class OnAuthenticatedUserListener
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(UserAuthenticatedEvent $event): void
    {
        Assert::email($event->email);

        $this->commandBus->dispatch(new CreateUserCommand(
            email: $event->email,
        ));
    }
}
