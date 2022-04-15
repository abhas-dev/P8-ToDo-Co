<?php

namespace App\DataFixtures;

use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        UserFactory::createOne(['username' => 'user', 'roles' => ['ROLE_USER'], 'tasks' => TaskFactory::new()->many(3)]);
        UserFactory::createOne(['username' => 'admin', 'roles' => ['ROLE_ADMIN'], 'tasks' => TaskFactory::new()->many(3)]);
        UserFactory::createMany(5, ['tasks' => TaskFactory::new()->many(3)]);
        TaskFactory::createMany(5);

        $manager->flush();
    }
}
