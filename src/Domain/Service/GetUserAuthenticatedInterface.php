<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Domain\Service;

use EmpireDesAmis\User\Domain\Entity\User;

interface GetUserAuthenticatedInterface
{
    public function getUser(): User;
}
