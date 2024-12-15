<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\Symfony\Security\Service;

use EmpireDesAmis\SecurityAuthenticatorBundle\Security\Service\GetUserAuthenticatedService as EmpireDesAmisGetUserAuthenticatedService;
use EmpireDesAmis\User\Domain\Entity\User;
use EmpireDesAmis\User\Domain\Repository\UserRepositoryInterface;
use EmpireDesAmis\User\Domain\Service\GetUserAuthenticatedInterface;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;

final readonly class GetUserAuthenticatedService implements GetUserAuthenticatedInterface
{
    public function __construct(
        private EmpireDesAmisGetUserAuthenticatedService $getUserAuthenticatedService,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    #[\Override]
    public function getUser(): User
    {
        $user = $this->getUserAuthenticatedService->getUser();

        return new User(
            $this->userRepository->nextIdentity(),
            UserEmail::fromString($user->email),
        );
    }
}
