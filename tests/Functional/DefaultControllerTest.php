<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DefaultControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    private $client = null;
    private User $testUser;

    public function setUp(): void
    {
        static::ensureKernelShutdown();
        $this->client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->testUser = $userRepository->findOneBy(['username' => 'test']);
    }

    public function test_homepage_is_up()
    {
        $this->client->loginUser($this->testUser);

        $this->client->request(Request::METHOD_GET, '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_homepage()
    {
        $this->client->loginUser($this->testUser);
        $crawler = $this->client->request(Request::METHOD_GET, '/');

        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue sur Todo List")')->count());
    }
}