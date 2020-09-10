<?php

class InitTaskTest extends \Codeception\Test\Unit
{
    /**
     * @var \TaskerTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testSomeFeature()
    {
        $this->tester->am("System");
        $this->tester->amGoingTo("init Board instance");

        $board = new \Gumeniukcom\ToDo\Board\Board(1, 'Board title');

        $this->tester->amGoingTo("init Status instance");

        $statusNew = new \Gumeniukcom\ToDo\Status\Status(1, 'New', $board->getId());

        $this->tester->amGoingTo("init task instance");

        $createdAt = new DateTimeImmutable('now');
        $task = new \Gumeniukcom\ToDo\Task\Task(1, 'Clean home', $board->getId(), $statusNew->getId(), $createdAt);

        $this->assertInstanceOf(\Gumeniukcom\ToDo\Task\Task::class, $task, '$task is instance of Gumeniukcom\Tasker\Objects\Task');

        $this->tester->comment("all ok");

    }
}