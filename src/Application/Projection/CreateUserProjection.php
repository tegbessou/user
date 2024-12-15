<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Application\Projection;

use EmpireDesAmis\User\Application\Exception\UserDoesntExistException;
use EmpireDesAmis\User\Application\Projection\Projector\CreateUserProjector;
use EmpireDesAmis\User\Domain\Event\UserCreated;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

#[WithMonologChannel('user')]
final readonly class CreateUserProjection
{
    public function __construct(
        private CreateUserProjector $createUserProjector,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(UserCreated $event): void
    {
        try {
            $this->createUserProjector->project(
                $event->id,
                $event->email,
            );
        } catch (UserDoesntExistException $exception) {
            $this->logger->error(
                'Create user projection: User projection creation failed',
                [
                    'exception' => $exception->getMessage(),
                ],
            );

            throw $exception;
        }
    }
}
