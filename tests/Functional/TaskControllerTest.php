<?php

namespace Tests\App\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TaskControllerTest extends WebTestCase
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

    // Utiliser yield et un generators pour tester les differentes url des taches
    public function test_redirection_on_login_if_not_authenticated()
    {
        $this->client->request(Request::METHOD_GET, '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function test_list_tasks_is_up()
    {
        $this->client->loginUser($this->testUser);

        $crawler = $this->client->request(Request::METHOD_GET, '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);
        static::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    // TODO: compter les taches
    public function test_list_tasks()
    {
        $this->client->loginUser($this->testUser);

        $crawler = $this->client->request(Request::METHOD_GET, '/tasks');

        static::assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    public function test_create_task()
    {
        $this->client->loginUser($this->testUser);

        $crawler = $this->client->request(Request::METHOD_GET, '/');

        $link = $crawler->selectLink('Créer une nouvelle tâche')->link();
        $crawler = $this->client->click($link);
        static::assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Finir le projet';
        $form['task[content]'] = 'Ecrire les tests';
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

    }

    // TODO
    public function test_edit_task()
    {
        $this->client->loginUser($this->testUser);

        $crawler = $this->client->request(Request::METHOD_GET, '/tasks/2/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }






//    public function test_a_task_is_related_to_a_user()
//    {
//    }
}