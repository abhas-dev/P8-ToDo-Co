<?php

namespace Tests\App\Functional;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TaskTest extends WebTestCase
{
    use ResetDatabase, Factories;

    private $client = null;
    private User $adminUser;
    private User $normalUser;

    public function setUp(): void
    {
        static::ensureKernelShutdown();
        $this->client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->adminUser = $userRepository->findOneBy(['username' => 'admin']);
        $this->normalUser = $userRepository->findOneBy(['username' => 'user']);
    }

    // Utiliser yield et un generators pour tester les differentes url des taches
    /**
     * @dataProvider provideUri
     * @param string $uri
     */
    public function test_redirection_on_login_if_not_authenticated(string $uri)
    {
        $this->client->request(Request::METHOD_GET, $uri);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertRouteSame('login');
    }

    public function test_list_tasks_is_up()
    {
        $this->client->loginUser($this->normalUser);

        $crawler = $this->client->request(Request::METHOD_GET, '/');

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);
        static::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_list_tasks()
    {
        $this->client->loginUser($this->normalUser);

        $crawler = $this->client->request(Request::METHOD_GET, '/tasks');

        static::assertSame(21, $crawler->filter('.thumbnail')->count());
        static::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_create_task()
    {
        $this->client->loginUser($this->normalUser);
        $totalTasks = sizeof($this->client->getContainer()->get(TaskRepository::class)->findAll());

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

        $newTotalTasks = sizeof($this->client->getContainer()->get(TaskRepository::class)->findAll());

        self::assertEquals($totalTasks + 1, $newTotalTasks);
    }

    // TODO
    public function test_edit_task()
    {
        $this->client->loginUser($this->normalUser);

        $crawler = $this->client->request(Request::METHOD_GET, '/tasks/2/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Titre modifié';
        $form['task[content]'] = 'Contenu modifié';
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertRouteSame('task_list');
        $this->assertSelectorExists('.alert.alert-success');

        /** @var Task $task */
        $task = $this->client->getContainer()->get(TaskRepository::class)->findOneBy(['id' => 2]);
        self::assertEquals('Titre modifié', $task->getTitle());
        self::assertEquals('Contenu modifié', $task->getContent());
    }

    public function test_toggle_task()
    {
        $this->client->loginUser($this->normalUser);
        /** @var Task $task */
        $oldTaskState = $this->client->getContainer()->get(TaskRepository::class)->find(2)->getIsDone();

        $this->client->request(Request::METHOD_GET, '/tasks/2/toggle');

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertRouteSame('task_list');
        $this->assertSelectorExists('.alert.alert-success');

        // TODO: verifier avec le crawler au lieu de bdd
        /** @var Task $task */
        $task = $this->client->getContainer()->get(TaskRepository::class)->find(2)->getIsDone();

        self::assertNotEquals($oldTaskState, $task);
    }

    public function test_cant_delete_if_not_owner()
    {
        $this->client->loginUser($this->normalUser);

        $this->client->request(Request::METHOD_GET, '/tasks/7/delete');

        $this->assertResponseStatusCodeSame(RESPONSE::HTTP_FORBIDDEN);

        $this->assertNotNull($this->getContainer()->get(TaskRepository::class)->find(2));
    }

//    // TODO: task with author anonymous
//    public function test_can_delete_if_owner_anonymous_and_authenticated_as_admin()
//    {
//        $this->client->loginUser($this->adminUser);
//
//        $this->client->request(Request::METHOD_GET, '/tasks/2/delete');
//
//        $this->assertResponseRedirects();
//        $this->client->followRedirect();
//        $this->assertRouteSame('task_list');
//        $this->assertSelectorExists('.alert.alert-success');
//
//        $this->assertNull($this->getContainer()->get(TaskRepository::class)->find(2));
//    }

    public function test_delete_task_as_owner()
    {
        $this->client->loginUser($this->normalUser);

        $this->client->request(Request::METHOD_GET, '/tasks/2/delete');

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertRouteSame('task_list');
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertNull($this->getContainer()->get(TaskRepository::class)->find(2));
    }

    public function provideUri(): Generator
    {
        yield 'list' => ['/tasks'];
        yield 'create' => ['/tasks/create'];
        yield 'edit' => ['/tasks/2/edit'];
        yield 'toggle' => ['/tasks/2/toggle'];
        yield 'delete' => ['/tasks/2/delete'];
    }




//    public function test_a_task_is_related_to_a_user()
//    {
//    }
}