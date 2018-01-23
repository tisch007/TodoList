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
}