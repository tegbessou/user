<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\Get;
use EmpireDesAmis\User\Infrastructure\ApiPlatform\State\Provider\UpProvider;

#[Get('/up', provider: UpProvider::class)]
final readonly class UpResource
{
}
