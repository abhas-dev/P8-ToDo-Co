<?php

namespace Tests\App\Unit;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    private User $user;
    private Task $task;

    public function setUp(): void
    {
        $this->user = new User();
        $this->task = new Task();

        $this->task
            ->setId(1)
            ->setTitle('title')
            ->setContent('content')
            ->setIsDone(true)
            ->setUser($this->user)
        ;
    }

    public function test_user_accessors_success(): void
    {
        $this->user
            ->setUsername('username')
            ->setEmail('true@email.com')
            ->setPassword('password')
            ->addTask($this->task);
        ;

        $this->assertEquals('username', $this->user->getUsername());
        $this->assertEquals('username', $this->user->getUserIdentifier());
        $this->assertEquals('true@email.com', $this->user->getEmail());
        $this->assertEquals(['ROLE_USER'], $this->user->getRoles());
        $this->assertEquals('password', $this->user->getPassword());
        $this->assertContainsEquals($this->task, $this->user->getTasks());
    }

    public function test_user_accessors_error(): void
    {
        $this->user
            ->setUsername('username')
            ->setEmail('true@email.com')
            ->setPassword('password')
        ;

        $this->assertNotEquals('false', $this->user->getUsername());
        $this->assertNotEquals('false', $this->user->getUserIdentifier());
        $this->assertNotEquals('false@email.com', $this->user->getEmail());
        $this->assertNotEquals([], $this->user->getRoles());
        $this->assertNotEquals('false', $this->user->getPassword());
    }

    public function test_user_empty()
    {
        $this->assertEmpty($this->user->getEmail());
        $this->assertEmpty($this->user->getUsername());
        $this->assertEmpty($this->user->getUserIdentifier());
        $this->assertEmpty($this->user->getPassword());
        $this->assertEmpty($this->user->getTasks());
    }

    public function test_remove_task()
    {
        $this->user->addTask($this->task);
        $this->assertContainsEquals($this->task, $this->user->getTasks());
        $this->user->removeTask($this->task);
        $this->assertNotContains($this->task, $this->user->getTasks());
    }
}
