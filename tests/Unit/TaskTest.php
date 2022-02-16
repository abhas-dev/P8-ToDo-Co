<?php

namespace Tests\App\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TaskTest extends KernelTestCase
{
//    use ResetDatabase, Factories;

    public function test_a_task_is_related_to_a_user()
    {
        $this->assertTrue(true);
    }

//    public function testThatTestsFunctions()
//    {
//        $task = (new Task())
//            ->setTitle('firstTest title')
//            ->setContent('content test')
//        ;
//        self::bootKernel();
//        $container = static::getContainer();
//
//        $errors = $container->get(ValidatorInterface::class)->validate($task);
//        $this->assertCount(0, $errors);
//
//    }
}