<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\Symfony\Security\Service;

use EmpireDesAmis\SecurityAuthenticatorBundle\Security\Service\GetUserAuthenticatedService as EmpireDesAmisGetUserAuthenticatedService;
use EmpireDesAmis\User\Domain\Entity\User;
use EmpireDesAmis\User\Domain\Service\GetUserAuthenticatedInterface;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;
use EmpireDesAmis\User\Domain\ValueObject\UserId;
use TegCorp\SharedKernelBundle\Domain\Factory\IdFactory;

final readonly class GetUserAuthenticatedService implements GetUserAuthenticatedInterface
{
    public function __construct(
        private EmpireDesAmisGetUserAuthenticatedService $getUserAuthenticatedService,
        private IdFactory $idFactory,
    ) {
    }

    #[\Override]
    public function getUser(): User
    {
        $user = $this->getUserAuthenticatedService->getUser();

        return new User(
            UserId::fromString($this->idFactory->create()),
            UserEmail::fromString($user->email),
        );
    }
}
