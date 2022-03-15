<?php

use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// ensure a fresh cache when debug mode is disabled
(new \Symfony\Component\Filesystem\Filesystem())->remove(__DIR__.'/../var/cache/test');

Zenstruck\Foundry\Test\TestState::addGlobalState(function () {
    UserFactory::createMany(5, ['tasks' => TaskFactory::new()->many(3)]);
    UserFactory::createOne(['username' => 'test', 'roles' => ['ROLE_ADMIN'], 'tasks' => TaskFactory::new()->many(3)]);
});
