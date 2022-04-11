<?php

namespace Tests\App\Functional;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class LoginTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

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

    // Dataprovider to add
//    public function test_successfull_login_as_normal_user()
//    {
//        $crawler = $this->client->request(Request::METHOD_GET, '/login');
//        $form = $crawler->selectButton('Se connecter')->form(self::createFormData());
//        $this->client->submit($form);
//        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
//
//        $this->client->enableProfiler();
//
//        if (($profile = $this->client->getProfile()) instanceof Profile) {
//            /** @var SecurityDataCollector $securityCollector */
//            $securityCollector = $profile->getCollector('security');
//
//            $this->assertTrue($securityCollector->isAuthenticated());
//        }
//
//        $this->client->followRedirect();
//        $this->assertRouteSame('homepage');
//    }

    public function test_successfull_login_as_admin()
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/login');
        $form = $crawler->selectButton('Se connecter')->form(self::createFormData());
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');

            $this->assertTrue($securityCollector->isAuthenticated());
            $this->assertEquals('ROLE_ADMIN', $securityCollector->getRoles()[0]);
        }

        $this->client->followRedirect();
        $this->assertRouteSame('homepage');
    }

    public function test_successfull_login_as_normal_user()
    {
        $crawler = $this->client->request(Request::METHOD_POST, '/login_check', [
            '_username' => 'user',
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

    /** @dataProvider provideBadData */
    public function test_should_show_errors(array $formData): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/login');
        $form = $crawler->selectButton('Se connecter')->form($formData);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');

            $this->assertFalse($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();
        $this->assertRouteSame('login');
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

    public function provideBadData(): Generator
    {
        yield 'bad username' => [self::createFormData(['_username' => 'fail'])];
        yield 'bad password' => [self::createFormData(['_password' => 'fail'])];
    }

    private static function createFormData(array $overrideData = []): array
    {
        return $overrideData + [
            '_username' => 'admin',
            '_password' => '12345678'
        ];
    }
}
