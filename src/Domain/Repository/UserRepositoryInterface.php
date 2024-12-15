<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Domain\Repository;

use EmpireDesAmis\User\Domain\Entity\User;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;
use EmpireDesAmis\User\Domain\ValueObject\UserId;

/**
 * Specifically, in the User entity we authorize this entity to be identified by its email and id, because for
 * authentication we need email, but to communicate between bounded contexts we need id.
 */
interface UserRepositoryInterface
{
    public function ofEmail(UserEmail $email): ?User;

    public function nextIdentity(): UserId;

    public function add(User $user): void;
}
