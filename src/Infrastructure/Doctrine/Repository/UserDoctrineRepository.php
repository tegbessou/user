<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use EmpireDesAmis\User\Domain\Entity\User;
use EmpireDesAmis\User\Domain\Repository\UserRepositoryInterface;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;
use EmpireDesAmis\User\Infrastructure\Doctrine\Entity\User as UserDoctrine;
use EmpireDesAmis\User\Infrastructure\Doctrine\Mapper\UserMapper;

final readonly class UserDoctrineRepository implements UserRepositoryInterface
{
    private const string ENTITY_CLASS = UserDoctrine::class;

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[\Override]
    public function ofEmail(UserEmail $email): ?User
    {
        $user = $this->entityManager->getRepository(self::ENTITY_CLASS)->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email->value())
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if ($user === null) {
            return null;
        }

        return UserMapper::toDomain($user);
    }

    #[\Override]
    public function add(User $user): void
    {
        $userDoctrine = UserMapper::toInfrastructurePersist($user);

        $this->entityManager->persist($userDoctrine);
        $this->entityManager->flush();
    }
}
