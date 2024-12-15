<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\Doctrine\Adapter;

use Doctrine\ODM\MongoDB\DocumentManager;
use EmpireDesAmis\User\Application\Adapter\UserAdapterInterface;
use EmpireDesAmis\User\Application\ReadModel\User;

final readonly class UserDoctrineAdapter implements UserAdapterInterface
{
    public function __construct(
        private DocumentManager $documentManager,
    ) {
    }

    #[\Override]
    public function ofId(string $id): ?User
    {
        return $this->documentManager->getRepository(User::class)->findOneBy([
            'email' => $id,
        ]);
    }

    #[\Override]
    public function add(User $user): void
    {
        $this->documentManager->persist($user);
        $this->documentManager->flush();
    }
}
