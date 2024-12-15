<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Application\Service;

use EmpireDesAmis\User\Domain\Event\UserCreated;

interface MessageBrokerInterface
{
    public function dispatchUserCreatedMessage(UserCreated $event): void;
}
