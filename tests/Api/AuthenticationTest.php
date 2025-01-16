<?php

namespace Api;

use App\Tests\Api\AbstractTest;
use Zenstruck\Foundry\Test\ResetDatabase;

class AuthenticationTest extends AbstractTest
{
    use ResetDatabase;

    public function testLogin(): void
    {
        $user = $this->createUser('password');

        self::ensureKernelShutdown();;

        $client = self::createClient();

        // retrieve a token
        $response = $client->request('POST', '/api/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $user->getEmail(),
                'password' => 'password',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $json = $response->toArray();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/test');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/test', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }

}