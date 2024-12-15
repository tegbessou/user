<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Application\Exception;

final class UserDoesntExistException extends \LogicException
{
    public function __construct(
        public readonly string $userId,
    ) {
        parent::__construct(sprintf('User %s does not exist', $this->userId));
    }
}
