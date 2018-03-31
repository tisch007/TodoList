<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTitle()
    {
        $task = new Task;
        $task->setTitle('testTitle');

        $this->assertEquals('testTitle', $task->getTitle());
    }

    public function testContent()
    {
        $task = new Task;
        $task->setContent('testContent');

        $this->assertEquals('testContent', $task->getContent());
    }

    public function testAuthor()
    {
        $task = new Task;
        $task->setAuthor('testAuthor');

        $this->assertEquals('testAuthor', $task->getAuthor());
    }

    public function testIsDone()
    {
        $task = new Task;
        $task->toggle(true);

        $this->assertEquals(true, $task->getIsDone());
    }

    public function testToggle()
    {
        $task = new Task;
        $this->assertEquals(false, $task->toggle(false));
    }

    public function testCreatedAt()
    {
        $task = new Task;
        $createdAt = new \Datetime();
        $task->setCreatedAt($createdAt);

        $this->assertEquals($createdAt, $task->getCreatedAt());
    }
}
