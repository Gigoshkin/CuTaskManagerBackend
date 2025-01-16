<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class CurrentUserProvider implements ProviderInterface
{

    public function __construct(
        private Security $security,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        if ($uriVariables['email'] !== $user->getEmail()) {
            return null;
        }

        return $user;
    }
}
