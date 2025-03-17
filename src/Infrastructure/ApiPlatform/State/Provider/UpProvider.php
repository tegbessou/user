<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use EmpireDesAmis\User\Infrastructure\ApiPlatform\Resource\UpResource;

/**
 * @implements ProviderInterface<UpResource>
 */
final class UpProvider implements ProviderInterface
{
    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): UpResource
    {
        return new UpResource();
    }
}
