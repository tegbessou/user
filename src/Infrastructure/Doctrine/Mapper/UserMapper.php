<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\Doctrine\Mapper;

use EmpireDesAmis\User\Domain\Entity\User;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;
use EmpireDesAmis\User\Domain\ValueObject\UserId;
use EmpireDesAmis\User\Infrastructure\Doctrine\Entity\User as UserDoctrine;

final readonly class UserMapper
{
    public static function toDomain(UserDoctrine $user): User
    {
        return new User(
            UserId::fromString($user->id),
            UserEmail::fromString($user->email),
        );
    }

    public static function toInfrastructurePersist(User $user): UserDoctrine
    {
        return new UserDoctrine(
            $user->id()->value(),
            $user->email()->value(),
        );
    }
}
