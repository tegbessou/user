<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Tests\AdapterTest\DrivingTest\Application\Projection;

use EmpireDesAmis\User\Application\Adapter\UserAdapterInterface;
use EmpireDesAmis\User\Application\Projection\CreateUserProjection;
use EmpireDesAmis\User\Domain\Entity\User;
use EmpireDesAmis\User\Domain\Event\UserCreated;
use EmpireDesAmis\User\Domain\Repository\UserRepositoryInterface;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;
use EmpireDesAmis\User\Domain\ValueObject\UserId;
use EmpireDesAmis\User\Tests\Shared\RefreshDatabase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CreateUserProjectionTest extends KernelTestCase
{
    use RefreshDatabase;

    private readonly CreateUserProjection $userProjection;
    private readonly UserAdapterInterface $userAdapter;
    private readonly UserRepositoryInterface $userRepository;

    public function testUserProjection(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $projection = $this->userProjection = $container->get(CreateUserProjection::class);
        $this->userRepository = $container->get(UserRepositoryInterface::class);
        $this->userAdapter = $container->get(UserAdapterInterface::class);

        $user = User::create(
            UserId::fromString('4ad98deb-4295-455d-99e2-66e148c162af'),
            UserEmail::fromString('pedro@gmail.com'),
        );
        $user::eraseRecordedEvents();

        $this->userRepository->add($user);

        $event = new UserCreated(
            '4ad98deb-4295-455d-99e2-66e148c162af',
            'pedro@gmail.com',
        );

        $projection($event);

        $user = $this->userAdapter->ofId('pedro@gmail.com');
        $this->assertNotNull($user);
    }
}
