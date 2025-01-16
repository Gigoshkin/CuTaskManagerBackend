<?php

namespace Api;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Api\AbstractTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordTest extends AbstractTest
{

    public function testChangePassword(): void
    {
        $user = $this->createUser('password1');
        $client = $this->createClientForUser($user, 'password1');


        $client->request('PATCH', '/api/change-password/' . $user->getEmail(), [
            'json' => [
                'password' => 'new_password18',
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();

        $response = $client->request('POST', '/api/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $user->getEmail(),
                'password' => 'new_password18',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $json = $response->toArray();
        $this->assertArrayHasKey('token', $json);
    }


}
