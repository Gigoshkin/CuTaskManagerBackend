<?php

namespace Api;

use App\Entity\Task;
use App\Entity\User;
use App\Factory\TaskFactory;
use App\Tests\Api\AbstractTest;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;

class RegisterTest extends AbstractTest
{

    public function testRegister(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/register', [
            'json' => [
                'email' => 'test@test.com',
                'password' => 'password',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            'email' => 'test@test.com',
        ]);

        $this->assertNotNull(
            static::getContainer()->get(EntityManagerInterface::class)->getRepository(User::class)->findOneBy(['email' => 'test@test.com'])
        );

    }


}
