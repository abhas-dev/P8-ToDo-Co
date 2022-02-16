<?php

namespace Tests\App\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    public function testCreateTask()
    {
        $client = self::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
//        $testUser = $userRepository->findOneByEmail('john.doe@example.com');

        // simulate $testUser being logged in
//        $client->loginUser($testUser);

        $client->request(Request::METHOD_GET, '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }



//    public function test_a_task_is_related_to_a_user()
//    {
//    }
}
