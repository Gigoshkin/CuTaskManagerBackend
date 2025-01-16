<?php

namespace App\Story;

use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class DefaultUsersAndTasksStory extends Story
{
    public function build(): void
    {
        UserFactory::createMany(10);

        TaskFactory::createMany(
            20,
            function() {
                return ['owner' => UserFactory::random()];
            }
        );
    }
}
