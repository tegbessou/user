<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use EmpireDesAmis\User\Infrastructure\Doctrine\Entity\User;

final class UserFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $users = [];
        $users[] = new User(
            'ee036f3b-d488-43be-b10c-fdbdcb0a6c0b',
            'hugues.gobet@gmail.com',
        );

        $users[] = new User(
            '05e8984e-45cd-44d4-8d42-f5c4e6bd6192',
            'root@gmail.com',
        );

        $users[] = new User(
            '246d37c8-c196-40bc-a8a8-c741ec8e3a13',
            'services.bottle_inventory@gmail.com',
        );

        $users[] = new User(
            'cf9f5035-23ca-4a48-bc5d-c5b25ff55f01',
            'services.tasting@gmail.com',
        );

        foreach ($users as $user) {
            $manager->persist($user);
        }

        $manager->flush();
    }
}
