<?php

use Gumeniukcom\ToDo\Board\BoardStorage;
use Gumeniukcom\ToDo\Status\StatusStorage;
use Gumeniukcom\ToDo\Task\TaskStorage;
use Psr\Log\NullLogger;
use \Psr\Log\LoggerInterface;

class TaskerTest extends \Codeception\Test\Unit
{
    /**
     * @var \TaskerTester
     */
    protected $tester;

    protected LoggerInterface $logger;

    /** @var StatusStorage */
    private StatusStorage $statusStorage;

    /** @var BoardStorage */
    private BoardStorage $boardStorage;

    /** @var TaskStorage */
    private TaskStorage $taskStorage;

    protected function _before()
    {
        $this->logger = new NullLogger();
        $this->taskStorage = new \Gumeniukcom\ToDo\Task\TaskInMemoryStorage($this->logger);
        $this->boardStorage = new \Gumeniukcom\ToDo\Board\BoardInMemoryStorage($this->logger);
        $this->statusStorage = new \Gumeniukcom\ToDo\Status\StatusInMemoryStorage($this->logger);
    }

    protected function _after()
    {
    }

    // tests
    public function testSomeFeature()
    {
        $tasker = new Gumeniukcom\Tasker\Service($this->logger, $this->statusStorage, $this->boardStorage, $this->taskStorage);

        $this->tester->am("User");

        $this->tester->amGoingTo("Create new Board");
        $board = $tasker->createBoard("New Board");
        $this->assertNotNull($board);

        $this->tester->amGoingTo("Create new Status NEW");
        $statusNew = $tasker->CreateStatus('NEW');
        $this->assertNotNull($statusNew);

        $this->tester->amGoingTo("Create new Status WIP");
        $statusWIP = $tasker->CreateStatus('WIP');
        $this->assertNotNull($statusWIP);

        $this->tester->amGoingTo("Create  new TASK");
        $task = $tasker->CreateTask('Fix home', $board, $statusNew);
        $this->assertNotNull($task);

        $this->tester->amGoingTo("Create  change task status to WIP");
        $tasker->ChangeTaskStatus($task, $statusWIP);
        $this->assertEquals($statusWIP, $task->getStatus(), "status WIP");

        $this->tester->amGoingTo("Create change task title to FOOBAR");
        $newTitle = 'FOOBAR';
        $result = $tasker->ChangeTask($task, $newTitle);
        $this->tester->assertTrue($result);
        $this->assertEquals($newTitle, $task->getTitle(), "title chaged");

        $this->tester->amGoingTo("Load task by id");
        $id = $task->getId();
        $loadedTask = $tasker->GetTaskById($id);
        $this->assertEquals($task, $loadedTask);

    }

    // tests
    public function testDeleteTask()
    {
        $tasker = new Gumeniukcom\Tasker\Service($this->logger, $this->statusStorage, $this->boardStorage, $this->taskStorage);

        $this->tester->am("User");

        $this->tester->amGoingTo("Create new Board");
        $board = $tasker->createBoard("New Board");
        $this->assertNotNull($board);

        $this->tester->amGoingTo("Create new Status NEW");
        $statusNew = $tasker->CreateStatus('NEW');
        $this->assertNotNull($statusNew);

        $this->tester->amGoingTo("Create new Status WIP");
        $statusWIP = $tasker->CreateStatus('WIP');
        $this->assertNotNull($statusWIP);

        $this->tester->amGoingTo("Create  new TASK");
        $task = $tasker->CreateTask('Fix home', $board, $statusNew);
        $this->assertNotNull($task);

        $id = $task->getId();

        $this->tester->amGoingTo("Create delete TASK");
        $result = $tasker->DeleteTask($task);
        $this->tester->assertTrue($result);

        $this->tester->amGoingTo("Load deletesd task by id");
        $loadedTask = $tasker->GetTaskById($id);
        $this->assertNull($loadedTask);

    }
}