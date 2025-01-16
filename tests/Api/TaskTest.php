<?php

namespace Api;

use App\Entity\Task;
use App\Factory\TaskFactory;
use App\Tests\Api\AbstractTest;
use DateTimeInterface;

class TaskTest extends AbstractTest
{

    public function testGetCollection(): void
    {

        $user = $this->createUser();

        TaskFactory::new()
            ->createMany(3, [
                'owner' => $user,
            ]);

        $this->assertCount(3, $user->getTasks());

        $client = $this->createClientForUser($user);

        $response = $client->request('GET', '/api/tasks');
        $responseData = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertEquals(3, $responseData['totalItems']);
        $this->assertMatchesResourceCollectionJsonSchema(Task::class);
    }

    public function testUserTasksNotAccessibleForOthers(): void
    {
        $user1 = $this->createUser('pass1');

        $this->assertCount(0, $user1->getTasks());

        TaskFactory::new()
            ->createMany(3, [
                'owner' => $user1,
            ]);

        $this->assertCount(3, $user1->getTasks());


        $user2 = $this->createUser('pass2');

        $this->assertCount(0, $user2->getTasks());

        TaskFactory::new()
            ->create([
                'owner' => $user2,
            ]);

        $this->assertCount(1, $user2->getTasks());

        $client = $this->createClientForUser($user1, 'pass1');

        $response = $client->request('GET', '/api/tasks');
        $responseData = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertEquals(3, $responseData['totalItems']);

        $iri = $this->findIriBy(Task::class, ['id' => $user2->getTasks()->first()->getId()]);
        $iris = array_column($responseData['member'], '@id');
        $this->assertNotContains($iri, $iris);
        $this->assertMatchesResourceCollectionJsonSchema(Task::class);
    }

    public function testCreateTask(): void
    {
        $user = $this->createUser();
        $client = $this->createClientForUser($user);

        $response = $client->request('POST', '/api/tasks', [
            'json' => [
                'nameEnglish' => 'Do Homework',
                'nameGeorgian' => 'საშინაო დავალება',
                'descriptionEnglish' => 'Task so I remember to do homework',
                'descriptionGeorgian' => 'Task so I remember to do homework',
                'expiresAt' => (new \DateTimeImmutable('tomorrow'))->format(DateTimeInterface::ATOM),
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Task',
            '@id' => '/api/tasks/1',
            '@type' => 'Task',
            'id' => 1,
            'nameEnglish' => 'Do Homework',
            'nameGeorgian' => 'საშინაო დავალება',
            'descriptionEnglish' => 'Task so I remember to do homework',
            'descriptionGeorgian' => 'Task so I remember to do homework',
            'expiresAt' => (new \DateTimeImmutable('tomorrow'))->format(DateTimeInterface::ATOM)
        ]);
        $this->assertMatchesResourceItemJsonSchema(Task::class);

    }

    public function testCreateInvalidTask(): void
    {
        $user = $this->createUser();
        $client = $this->createClientForUser($user);

        $client->request('POST', '/api/tasks', [
            'json' => [
                'nameEnglish' => '',
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'title' => 'An error occurred',
            'description' => 'nameEnglish: This value should not be blank.
nameGeorgian: This value should not be blank.
expiresAt: This value should not be null.',
        ]);

    }


    public function testUpdateTask(): void
    {
        $user = $this->createUser();
        $client = $this->createClientForUser($user);

        $task = TaskFactory::new()
            ->create(['owner' => $user, 'nameEnglish' => 'Old Name']);

        $iri = $this->findIriBy(Task::class, ['id' => $task->getId()]);

        $client->request('PATCH', $iri, [
            'json' => [
                'nameEnglish' => 'New Name',
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@id' => $iri,
            'id' => $task->getId(),
            'nameEnglish' => 'New Name',
        ]);
    }

    public function testUserCantDeleteOthersTask(): void
    {
        $user1 = $this->createUser();

        $user2 = $this->createUser();
        $task = TaskFactory::new()
            ->create(['owner' => $user2]);

        $this->assertCount(1, $user2->getTasks());

        $iri = $this->findIriBy(Task::class, ['id' => $task->getId()]);

        $client = $this->createClientForUser($user1);

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(404);
        $this->assertCount(1, $user2->getTasks());
    }

    public function testDeleteTask(): void
    {
        $user1 = $this->createUser();

        $task = TaskFactory::new()
            ->create(['owner' => $user1]);

        $this->assertCount(1, $user1->getTasks());

        $iri = $this->findIriBy(Task::class, ['id' => $task->getId()]);

        $client = $this->createClientForUser($user1);

        $response = $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
        $this->assertCount(0, $user1->getTasks());
    }
}
