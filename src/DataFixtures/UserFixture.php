<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('info@magnum.com');
        $user->setPassword('123');

        $token = new ApiToken($user);

        $manager->persist($user);
        $manager->persist($token);
        $manager->flush();
    }
}
