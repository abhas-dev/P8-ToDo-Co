<?php

namespace Tests\App\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    private $client = null;
    private User $adminUser;
    private User $normalUser;

    public function setUp(): void
    {
        static::ensureKernelShutdown();
        $this->client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->normalUser = $userRepository->findOneBy(['username' => 'user']);
        $this->adminUser = $userRepository->findOneBy(['username' => 'admin']);
    }

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

//    /**
//     * @dataProvider provideUri
//     * @param string $uri
//     */
//    public function test_redirection_on_login_if_not_authenticated_as_admin(string $uri)
//    {
//        $this->client->loginUser($this->normalUser);
//
//        $this->client->request(Request::METHOD_GET, $uri);
//
//        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
//        $this->client->followRedirect();
//        $this->assertRouteSame('login');
//    }
//
//    /**
//     * @dataProvider provideUri
//     * @param string $uri
//     */
//    public function test_successful_access_if_authenticated_as_admin(string $uri)
//    {
//        $this->client->loginUser($this->adminUser);
//
//        $this->client->request(Request::METHOD_GET, $uri);
//
//            $this->assertResponseIsSuccessful();
//    }

    public function test_list_users()
    {
        $this->client->loginUser($this->adminUser);

        $crawler = $this->client->request(Request::METHOD_GET, '/users');

        static::assertResponseStatusCodeSame(Response::HTTP_OK);
        static::assertSame(8, $crawler->filter('tr')->count());
    }

    public function test_create_user()
    {
        $this->client->loginUser($this->adminUser);
        $totalUsers = sizeof($this->client->getContainer()->get(UserRepository::class)->findAll());

        $crawler = $this->client->request(Request::METHOD_GET, '/');
        $link = $crawler->selectLink('Créer un utilisateur')->link();
        $crawler = $this->client->click($link);

        static::assertRouteSame('user_create');
        static::assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'newUser';
        $form['user[password][first]'] = '12345678';
        $form['user[password][second]'] = '12345678';
        $form['user[email]'] = 'newuser@test.fr';
        $form['user[roles]'] = 'ROLE_USER';
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $newTotalUsers = sizeof($this->client->getContainer()->get(UserRepository::class)->findAll());

        $this->assertEquals($totalUsers + 1, $newTotalUsers);
        $this->assertEquals('newUser', $this->client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'newuser@test.fr'])->getUsername());
    }

    public function test_edit_user()
    {
        $this->client->loginUser($this->adminUser);

        $crawler = $this->client->request(Request::METHOD_GET, '/users/2/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'modifiedUser';
        $form['user[password][first]'] = '12345678';
        $form['user[password][second]'] = '12345678';
        $form['user[email]'] = 'modifiedUser@test.fr';
        $form['user[roles]'] = 'ROLE_ADMIN';
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertEquals('modifiedUser', $this->client->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'modifiedUser@test.fr'])->getUsername());
    }

    public function provideUri(): Generator
    {
        yield 'list' => ['/users'];
        yield 'create' => ['/users/create'];
        yield 'edit' => ['/users/2/edit'];
    }
}
