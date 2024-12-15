<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(name: 'id', type: 'uuid', length: 36, unique: true)]
        public string $id,
        #[ORM\Column(name: 'email', unique: true)]
        public string $email,
    ) {
    }
}
