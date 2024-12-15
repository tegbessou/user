<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Application\Command;

use EmpireDesAmis\User\Domain\Entity\User;
use EmpireDesAmis\User\Domain\Repository\UserRepositoryInterface;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;
use TegCorp\SharedKernelBundle\Application\Command\AsCommandHandler;
use TegCorp\SharedKernelBundle\Domain\Service\DomainEventDispatcherInterface;

#[AsCommandHandler]
final readonly class CreateUserCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DomainEventDispatcherInterface $dispatcher,
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        if ($this->userRepository->ofEmail(UserEmail::fromString($command->email))) {
            return;
        }

        $user = User::create(
            $this->userRepository->nextIdentity(),
            UserEmail::fromString($command->email),
        );

        $this->userRepository->add($user);

        $this->dispatcher->dispatch($user);
    }
}
