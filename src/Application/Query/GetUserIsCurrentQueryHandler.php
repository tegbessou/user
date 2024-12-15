<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Application\Query;

use EmpireDesAmis\User\Application\Adapter\UserAdapterInterface;
use EmpireDesAmis\User\Application\ReadModel\User;
use EmpireDesAmis\User\Domain\Service\GetUserAuthenticatedInterface;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;
use EmpireDesAmis\User\Domain\ValueObject\UserIsCurrent;
use TegCorp\SharedKernelBundle\Application\Query\AsQueryHandler;

#[AsQueryHandler]
final readonly class GetUserIsCurrentQueryHandler
{
    public function __construct(
        private UserAdapterInterface $userAdapter,
        private GetUserAuthenticatedInterface $getUserAuthenticated,
    ) {
    }

    public function __invoke(GetUserIsCurrentQuery $getUserIsCurrentQuery): ?UserIsCurrent
    {
        $user = $this->userAdapter->ofId($getUserIsCurrentQuery->email);

        if ($user === null) {
            return null;
        }

        return UserIsCurrent::create(
            UserEmail::fromString($user->email),
            $this->isCurrentOrService($user),
        );
    }

    private function isCurrentOrService(
        User $user,
    ): bool {
        return $this->getUserAuthenticated->getUser()->email()->equals(UserEmail::fromString($user->email))
            || str_contains($this->getUserAuthenticated->getUser()->email()->value(), 'services')
        ;
    }
}
