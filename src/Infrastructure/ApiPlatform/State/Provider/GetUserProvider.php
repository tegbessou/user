<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use EmpireDesAmis\User\Application\Query\GetUserIsCurrentQuery;
use EmpireDesAmis\User\Infrastructure\ApiPlatform\Resource\GetUserResource;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TegCorp\SharedKernelBundle\Application\Query\QueryBusInterface;

/**
 * @implements ProviderInterface<GetUserResource>
 */
#[WithMonologChannel('user')]
final readonly class GetUserProvider implements ProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): GetUserResource
    {
        $user = $this->queryBus->ask(
            new GetUserIsCurrentQuery(
                $uriVariables['email'],
            ),
        );

        if ($user === null) {
            $this->logger->error(
                'Get user: User not found',
                [
                    'email' => $uriVariables['email'],
                ],
            );

            throw new NotFoundHttpException();
        }

        return GetUserResource::fromValue($user);
    }
}
