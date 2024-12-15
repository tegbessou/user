<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use EmpireDesAmis\User\Application\ReadModel\User;
use EmpireDesAmis\User\Domain\ValueObject\UserIsCurrent;
use EmpireDesAmis\User\Infrastructure\ApiPlatform\State\Provider\GetUserProvider;

#[ApiResource(
    shortName: 'User',
)]
#[Get(
    '/users/{email}',
    requirements: [
        'email' => '.+',
    ],
    provider: GetUserProvider::class,
)]
final readonly class GetUserResource
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public ?string $email = null,
        #[ApiProperty]
        public bool $isCurrent = false,
    ) {
    }

    public static function fromValue(
        UserIsCurrent $userIsCurrent,
    ): self {
        return new self(
            $userIsCurrent->email()->value(),
            $userIsCurrent->isCurrent(),
        );
    }

    public static function fromModel(
        User $user,
    ): self {
        return new self(
            $user->email,
            true,
        );
    }
}
