<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Tests\AdapterTest\DrivingTest\Infrastructure\Symfony\Listener;

use EmpireDesAmis\SecurityAuthenticatorBundle\Event\UserAuthenticatedEvent;
use EmpireDesAmis\User\Domain\Repository\UserRepositoryInterface;
use EmpireDesAmis\User\Domain\ValueObject\UserEmail;
use EmpireDesAmis\User\Infrastructure\Symfony\Listener\OnAuthenticatedUserListener;
use EmpireDesAmis\User\Infrastructure\Symfony\Messenger\Message\UserCreatedMessage;
use EmpireDesAmis\User\Tests\Shared\RefreshDatabase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

final class OnAuthenticatedUserListenerTest extends KernelTestCase
{
    use InteractsWithMessenger;
    use RefreshDatabase;

    private UserRepositoryInterface $userRepository;
    private OnAuthenticatedUserListener $eventListener;

    protected function setUp(): void
    {
        $container = self::getContainer();
        $this->userRepository = $container->get(UserRepositoryInterface::class);
        $this->eventListener = $container->get(OnAuthenticatedUserListener::class);
    }

    public function testOnAuthenticatedUser(): void
    {
        $eventListener = $this->eventListener;
        $eventListener(
            new UserAuthenticatedEvent('nexistepas@gmail.com'),
        );

        $this->transport('user_to_tasting')->queue()->assertContains(UserCreatedMessage::class, 1);
        $this->transport('user_to_tasting')->reset();

        $user = $this->userRepository->ofEmail(
            UserEmail::fromString('nexistepas@gmail.com'),
        );

        $this->assertNotNull($user);
    }
}
