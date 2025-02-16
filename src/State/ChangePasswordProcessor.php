<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class ChangePasswordProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    public function process( $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {

//        if (!$data->getPlainPassword()) {
//            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
//        }
//
//        $hashedPassword = $this->userPasswordHasher->hashPassword(
//            $data,
//            $data->getApi()
//        );
//        $data->setPassword($hashedPassword);
//        $data->eraseCredentials();
//
//        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        /* @var User $data */
        $data->setPassword($this->userPasswordHasher->hashPassword($data, $data->getApiPlainPassword()));
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

}
