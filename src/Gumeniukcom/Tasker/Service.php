<?php declare(strict_types=1);


namespace Gumeniukcom\Tasker;


use Gumeniukcom\AbstractService\LoggerTrait;
use Gumeniukcom\ToDo\Board\Board;
use Gumeniukcom\ToDo\Board\BoardStorage;
use Gumeniukcom\ToDo\Status\Status;
use Gumeniukcom\ToDo\Status\StatusStorage;
use Gumeniukcom\ToDo\Task\Task;
use Gumeniukcom\ToDo\Task\TaskStorage;
use Psr\Log\LoggerInterface;

class Service implements TaskCRUDInterface, StatusCRUDInterface, BoardCRUDInterface
{
    use LoggerTrait;

    /** @var StatusStorage */
    private StatusStorage $statusStorage;

    /** @var BoardStorage */
    private BoardStorage $boardStorage;

    /** @var TaskStorage */
    private TaskStorage $taskStorage;

    /**
     * Service constructor.
     * @param LoggerInterface $logger
     * @param StatusStorage $statusStorage
     * @param BoardStorage $boardStorage
     * @param TaskStorage $taskStorage
     */
    public function __construct(LoggerInterface $logger, StatusStorage $statusStorage, BoardStorage $boardStorage, TaskStorage $taskStorage)
    {
        $this->logger = $logger;
        $this->statusStorage = $statusStorage;
        $this->boardStorage = $boardStorage;
        $this->taskStorage = $taskStorage;
    }

    /**
     * @param string $title
     * @return Board|null
     */
    public function createBoard(string $title): ?Board
    {
        $board = $this->boardStorage->New($title);
        if ($board === null) {
            $this->logger->error("error on get new Board",
                ['title' => $title]
            );
            return null;
        }

        $this->logger->debug("new board created",
            ['title' => $title]
        );
        return $board;
    }

    /**
     * @param string $title
     * @return Status|null
     */
    public function CreateStatus(string $title): ?Status
    {
        $status = $this->statusStorage->New($title);
        if ($status === null) {
            $this->logger->error("error on get new Status",
                ['title' => $title]
            );
            return null;
        }

        $this->logger->debug("new status created",
            ['title' => $title]
        );
        return $status;
    }

    /**
     * @param string $title
     * @param Board $board
     * @param Status $status
     * @return Task|null
     */
    public function CreateTask(string $title, Board $board, Status $status): ?Task
    {
        try {
            $createdAt = new \DateTimeImmutable();
        } catch (\Exception $e) {
            $this->logger->error("Error on create data", ['exception' => $e]);

            return null;
        }

        $task = $this->taskStorage->New($title, $board, $status, $createdAt);

        if ($task === null) {
            $this->logger->error("error on get new tasks",
                [
                    'title' => $title,
                    'board_id' => $board,
                    'status_id' => $status,
                    'created_at' => $createdAt->format(\DateTime::ISO8601),
                ]
            );
            return null;
        }

        $this->logger->debug("new tasks created",
            [
                'title' => $title,
                'board_id' => $board,
                'status_id' => $status,
                'created_at' => $createdAt->format(\DateTime::ISO8601),
            ]
        );

        return $task;
    }

    /**
     * @param Task $task
     * @param Status $status
     * @return bool
     */
    public function ChangeTaskStatus(Task $task, Status $status): bool
    {
        $oldStatus = $task->getStatus();
        $task->setStatus($status);

        try {
            $updatedAt = new \DateTime();
        } catch (\Exception $e) {
            $this->logger->error("Error on create data", ['exception' => $e]);

            return false;
        }

        $task->setUpdatedAt($updatedAt);

        $result = $this->taskStorage->Set($task);

        if (!$result) {
            $this->logger->error("failed update task status",
                [
                    'task_id' => $task->getId(),
                    'new_status_id' => $status->getId(),
                    'old_status_id' => $oldStatus->getId(),
                ]
            );
            return false;
        }

        $this->logger->debug("updated task status",
            [
                'task_id' => $task->getId(),
                'new_status_id' => $status->getId(),
                'old_status_id' => $oldStatus->getId(),
            ]
        );

        return true;
    }

    /**
     * @param Task $task
     * @param string $title
     * @return bool
     */
    public function ChangeTask(Task $task, string $title): bool
    {
        $oldTitle = $task->getTitle();

        $task->setTitle($title);

        try {
            $updatedAt = new \DateTime();
        } catch (\Exception $e) {
            $this->logger->error("Error on create data", ['exception' => $e]);

            return false;
        }

        $task->setUpdatedAt($updatedAt);

        $result = $this->taskStorage->Set($task);

        if (!$result) {
            $this->logger->error("failed update task",
                [
                    'task_id' => $task->getId(),
                    'new_title' => $title,
                    'old_title' => $oldTitle,
                ]
            );
            return false;
        }

        $this->logger->debug("updated task",
            [
                'task_id' => $task->getId(),
                'new_title' => $title,
                'old_title' => $oldTitle,
            ]
        );

        return true;
    }

    /**
     * @param int $id
     * @return Task|null
     */
    public function GetTaskById(int $id): ?Task
    {
        $task = $this->taskStorage->Load($id);
        if ($task === null) {
            $this->logger->info("task not found by id",
                [
                    'task_id' => $id,
                ]
            );
            return null;
        }

        $this->logger->info("task found by id",
            [
                'task_id' => $id,
            ]
        );
        return $task;
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function DeleteTask(Task $task): bool
    {
        $result = $this->taskStorage->Delete($task);

        if ($result === false) {
            $this->logger->error("error on delete task",
                [
                    'task_id' => $task->getId(),
                ]
            );
            return false;
        }
        $this->logger->error("deleted task",
            [
                'task_id' => $task->getId(),
            ]
        );
        return true;
    }

    public function getBoardById(int $id): ?Board
    {
        $board = $this->boardStorage->Load($id);

        if ($board === null) {
            $this->logger->info("board not found by id",
                [
                    'board_id' => $id,
                ]
            );
            return null;
        }

        $this->logger->info("board found by id",
            [
                'board_id' => $id,
            ]
        );
        return $board;
    }

    public function changeBoard(Board $board, string $title): bool
    {
        $oldTitle = $board->getTitle();

        $board->setTitle($title);

        $result = $this->boardStorage->Set($board);

        if (!$result) {
            $this->logger->error("failed update task",
                [
                    'board_id' => $board->getId(),
                    'new_title' => $title,
                    'old_title' => $oldTitle,
                ]
            );
            return false;
        }

        $this->logger->debug("updated task",
            [
                'board_id' => $board->getId(),
                'new_title' => $title,
                'old_title' => $oldTitle,
            ]
        );

        return true;
    }

    /**
     * @param Board $board
     * @return bool
     */
    public function deleteBoard(Board $board): bool
    {
        $result = $this->boardStorage->Delete($board);

        if ($result === false) {
            $this->logger->error("error on delete board",
                [
                    'board_id' => $board->getId(),
                ]
            );
            return false;
        }
        $this->logger->error("deleted task",
            [
                'board_id' => $board->getId(),
            ]
        );
        return true;
    }

    /**
     * @param int $id
     * @return Status|null
     */
    public function getStatusById(int $id): ?Status
    {
        $status = $this->statusStorage->Load($id);

        if ($status === null) {
            $this->logger->info("status not found by id",
                [
                    'status_id' => $id,
                ]
            );
            return null;
        }

        $this->logger->info("status found by id",
            [
                'status_id' => $id,
            ]
        );
        return $status;
    }

    /**
     * @param Status $status
     * @param string $title
     * @return bool
     */
    public function changeStatus(Status $status, string $title): bool
    {
        $oldTitle = $status->getTitle();

        $status->setTitle($title);

        $result = $this->statusStorage->Set($status);

        if (!$result) {
            $this->logger->error("failed update task",
                [
                    'status_id' => $status->getId(),
                    'new_title' => $title,
                    'old_title' => $oldTitle,
                ]
            );
            return false;
        }

        $this->logger->debug("updated task",
            [
                'status_id' => $status->getId(),
                'new_title' => $title,
                'old_title' => $oldTitle,
            ]
        );

        return true;
    }

    /**
     * @param Status $status
     * @return bool
     */
    public function deleteStatus(Status $status): bool
    {
        $result = $this->statusStorage->Delete($status);

        if ($result === false) {
            $this->logger->error("error on delete status",
                [
                    'status_id' => $status->getId(),
                ]
            );
            return false;
        }
        $this->logger->error("deleted status",
            [
                'status_id' => $status->getId(),
            ]
        );
        return true;
    }
}