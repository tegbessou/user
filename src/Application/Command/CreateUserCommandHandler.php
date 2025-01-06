<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Application\Command;

use EmpireDesAmis\User\Domain\Entity\User;
use EmpireDesAmis\User\Domain\Repository\UserRepositoryInterface;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;
use EmpireDesAmis\User\Domain\ValueObject\UserId;
use TegCorp\SharedKernelBundle\Application\Command\AsCommandHandler;
use TegCorp\SharedKernelBundle\Domain\Factory\IdFactory;
use TegCorp\SharedKernelBundle\Domain\Service\DomainEventDispatcherInterface;

#[AsCommandHandler]
final readonly class CreateUserCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DomainEventDispatcherInterface $dispatcher,
        private IdFactory $idFactory,
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        if ($this->userRepository->ofEmail(UserEmail::fromString($command->email))) {
            return;
        }

        $user = User::create(
            UserId::fromString($this->idFactory->create()),
            UserEmail::fromString($command->email),
        );

        $this->userRepository->add($user);

        $this->dispatcher->dispatch($user);
    }
}
