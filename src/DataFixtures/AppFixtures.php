<?php

namespace App\DataFixtures;

use App\Story\DefaultUsersAndTasksStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultUsersAndTasksStory::load();

        $manager->flush();
    }
}
