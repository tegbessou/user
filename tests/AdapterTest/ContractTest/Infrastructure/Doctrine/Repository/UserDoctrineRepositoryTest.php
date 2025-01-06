<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Tests\AdapterTest\ContractTest\Infrastructure\Doctrine\Repository;

use EmpireDesAmis\User\Domain\Entity\User;
use EmpireDesAmis\User\Domain\Repository\UserRepositoryInterface;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;
use EmpireDesAmis\User\Domain\ValueObject\UserId;
use EmpireDesAmis\User\Tests\Shared\RefreshDatabase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UserDoctrineRepositoryTest extends KernelTestCase
{
    use RefreshDatabase;

    private UserRepositoryInterface $doctrineUserRepository;

    #[\Override]
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->doctrineUserRepository = $container->get(UserRepositoryInterface::class);
    }

    public function testOfEmail(): void
    {
        $user = $this->doctrineUserRepository->ofEmail(UserEmail::fromString('hugues.gobet@gmail.com'));

        $this->assertNotNull($user);
    }

    public function testOfEmailNull(): void
    {
        $user = $this->doctrineUserRepository->ofEmail(UserEmail::fromString('pedro@gmail.com'));

        $this->assertNull($user);
    }

    public function testAddUser(): void
    {
        $user = User::create(
            UserId::fromString('af785dbb-4ac1-4786-a5aa-1fed08f6ec26'),
            UserEmail::fromString('pedro@gmail.com'),
        );

        $this->doctrineUserRepository->add($user);

        $user = $this->doctrineUserRepository->ofEmail(UserEmail::fromString('pedro@gmail.com'));

        $this->assertNotNull($user);

        $user::eraseRecordedEvents();
    }
}
