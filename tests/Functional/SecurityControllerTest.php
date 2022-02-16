<?php

namespace Tests\App\Functional;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use http\Client;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SecurityControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    public function test_display_login()
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function test_nologin_with_bad_credentials()
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/login');
        $form =
            $crawler->selectButton('Se connecter')->form([
                '_username' => 'test',
                '_password' => 'test with wrong password'
            ]);
        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function test_successfull_login()
    {
        UserFactory::createOne(['username' => 'test', 'roles' => ['ROLE_ADMIN']]);
        static::ensureKernelShutdown(); // creating factories boots the kernel; shutdown before creating the client
        $client = static::createClient();

//        $crawler = $client->request(Request::METHOD_GET, '/login');
//        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
//
//        $client->submitForm('Se connecter', [
//            '_username' => 'test',
//            '_password' => '12345678',
//        ]);
//
//        $client->followRedirect();
//        $this->assertRouteSame('/');

        $crawler = $client->request(Request::METHOD_POST, '/login_check', [
            '_username' => 'test',
            '_password' => '12345678',
        ]);
//        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $client->followRedirect();
        $this->assertRouteSame('homepage');

    }
    // TODO: test de bonne redirection

}