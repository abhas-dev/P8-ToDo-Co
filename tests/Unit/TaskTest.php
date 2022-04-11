<?php

namespace Tests\App\Unit;

use App\Entity\Task;
use App\Entity\User;
use Monolog\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    private User $user;
    private Task $task;

    public function setUp(): void
    {
        $this->user = new User();
        $this->task = new Task();
    }

    public function test_task_accessors_success(): void
    {
        $this->task
            ->setTitle('title')
            ->setContent('content')
            ->setIsDone(true)
            ->setUser($this->user)
        ;

        $this->assertEquals('title', $this->task->getTitle());
        $this->assertEquals('content', $this->task->getContent());
        $this->assertTrue($this->task->getIsDone());
        $this->assertEquals($this->user, $this->task->getUser());
    }

    public function test_task_accessors_error(): void
    {
        $this->task
            ->setTitle('title')
            ->setContent('content')
            ->setIsDone(true)
            ->setUser(null)
        ;

        $this->assertNotEquals('not title', $this->task->getTitle());
        $this->assertNotEquals('not content', $this->task->getContent());
        $this->assertNotEquals(false, $this->task->getIsDone());
        $this->assertNotEquals($this->user, $this->task->getUser());
    }

    public function test_task_empty()
    {
        $this->assertEmpty($this->task->getTitle());
        $this->assertEmpty($this->task->getContent());
        $this->assertEmpty($this->task->getIsDone());
        $this->assertEmpty($this->task->getUser());
    }
}