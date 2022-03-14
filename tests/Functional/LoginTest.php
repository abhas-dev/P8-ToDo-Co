<?php

namespace Tests\App\Functional;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use http\Client;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class LoginTest extends WebTestCase
{
    use ResetDatabase, Factories;

    private $client;

    public function setUp(): void
    {
        static::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    public function test_display_login()
    {
        $this->client->request(Request::METHOD_GET, '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function test_no_login_with_bad_credentials()
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/login');
        $form =
            $crawler->selectButton('Se connecter')->form([
                '_username' => 'test',
                '_password' => 'test with wrong password'
            ]);
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function test_successfull_login()
    {
        $crawler = $this->client->request(Request::METHOD_POST, '/login_check', [
            '_username' => 'test',
            '_password' => '12345678',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');

            $this->assertTrue($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();
        $this->assertRouteSame('homepage');

    }
    // TODO: test de bonne redirection

}