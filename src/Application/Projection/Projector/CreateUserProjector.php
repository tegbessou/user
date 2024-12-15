<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Application\Projection\Projector;

use EmpireDesAmis\User\Application\Adapter\UserAdapterInterface;
use EmpireDesAmis\User\Application\Exception\UserDoesntExistException;
use EmpireDesAmis\User\Application\ReadModel\User;
use EmpireDesAmis\User\Domain\Repository\UserRepositoryInterface;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;

final readonly class CreateUserProjector
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserAdapterInterface $userAdapter,
    ) {
    }

    public function project(
        string $id,
        string $email,
    ): void {
        $user = $this->userRepository->ofEmail(
            UserEmail::fromString($email),
        );

        if ($user === null) {
            throw new UserDoesntExistException($id);
        }

        $user = new User(
            $id,
            $email,
        );

        $this->userAdapter->add($user);
    }
}
