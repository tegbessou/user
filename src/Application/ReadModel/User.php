<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Application\ReadModel;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class User
{
    public function __construct(
        #[MongoDB\Id(strategy: 'UUID')]
        public string $id,
        #[MongoDB\Field(type: 'string')]
        public string $email,
    ) {
    }
}
