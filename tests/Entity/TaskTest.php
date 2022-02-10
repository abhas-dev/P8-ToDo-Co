<?php

namespace Tests\App\Entity;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskTest extends KernelTestCase
{
    public function testThatTestsFunctions()
    {
        $task = (new Task())
            ->setTitle('firstTest title')
            ->setContent('content test')
        ;
        self::bootKernel();
        $container = static::getContainer();

        $errors = $container->get(ValidatorInterface::class)->validate($task);
        $this->assertCount(0, $errors);

    }
}