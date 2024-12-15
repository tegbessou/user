<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Application\Adapter;

use EmpireDesAmis\User\Application\ReadModel\User;

interface UserAdapterInterface
{
    public function ofId(string $id): ?User;

    public function add(User $user): void;
}
