<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractTest extends ApiTestCase
{

    use ResetDatabase, Factories;

    private ?string $token = null;

    private const DEFAULT_PASS = 'pass';

    public function setUp(): void
    {
        self::bootKernel();
    }

    protected function createUser(string $password = self::DEFAULT_PASS): User
    {
        $user = UserFactory::new()
            ->create([
                'password' => $password,
            ]);

        $this->assertCount(0, $user->getTasks());

        return $user;
    }

    protected function createClientWithCredentials($token): Client
    {
        static::ensureKernelShutdown();
        return static::createClient([], ['headers' => ['authorization' => 'Bearer ' . $token]]);
    }

    protected function getToken(string $email, string $password = self::DEFAULT_PASS): string
    {
        if ($this->token) {
            return $this->token;
        }

        $response = static::createClient()->request(
            'POST',
            '/api/auth',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ],
            ]
        );

        $json = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        $this->token = $json['token'];
        return $json['token'];
    }

    protected function createClientForUser(User $user, string $password = self::DEFAULT_PASS): Client
    {
        $token = $this->getToken($user->getUserIdentifier(), $password);
        return $this->createClientWithCredentials($token);
    }


}