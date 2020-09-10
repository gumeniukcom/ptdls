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
        $board = $this->boardStorage->new($title);
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
     * @param Board $board
     * @return Status|null
     */
    public function createStatus(string $title, Board $board): ?Status
    {
        $status = $this->statusStorage->new($title, $board->getId());
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
    public function createTask(string $title, Board $board, Status $status): ?Task
    {

        $task = $this->taskStorage->new($title, $board->getId(), $status->getId());

        if ($task === null) {
            $this->logger->error("error on get new tasks",
                [
                    'title' => $title,
                    'board_id' => $board,
                    'status_id' => $status,
                ]
            );
            return null;
        }

        $this->logger->debug("new tasks created",
            [
                'title' => $title,
                'board_id' => $board,
                'status_id' => $status,
            ]
        );

        return $task;
    }

    /**
     * @param Task $task
     * @param Status $status
     * @return bool
     */
    public function changeTaskStatus(Task $task, Status $status): bool
    {
        $oldStatus = $task->getStatusId();
        $task->setStatusId($status->getId());

        try {
            $updatedAt = new \DateTime();
        } catch (\Exception $e) {
            $this->logger->error("Error on create data", ['exception' => $e]);

            return false;
        }

        $task->setUpdatedAt($updatedAt);

        $result = $this->taskStorage->set($task, $oldStatus);

        if (!$result) {
            $this->logger->error("failed update task status",
                [
                    'task_id' => $task->getId(),
                    'new_status_id' => $status->getId(),
                    'old_status_id' => $oldStatus,
                ]
            );
            return false;
        }

        $this->logger->debug("updated task status",
            [
                'task_id' => $task->getId(),
                'new_status_id' => $status->getId(),
                'old_status_id' => $oldStatus,
            ]
        );

        return true;
    }

    /**
     * @param Task $task
     * @param string|null $title
     * @param int|null $statusId
     * @return bool
     */
    public function changeTask(Task $task, ?string $title = null, ?int $statusId = null): bool
    {
        $oldTitle = $task->getTitle();
        $oldStatusId = $task->getStatusId();

        if ($title !== null) {
            $task->setTitle($title);
        }

        if ($statusId !== null) {
            $task->setStatusId($statusId);
        }


        try {
            $updatedAt = new \DateTime();
        } catch (\Exception $e) {
            $this->logger->error("Error on create data", ['exception' => $e]);

            return false;
        }

        $task->setUpdatedAt($updatedAt);

        $result = $this->taskStorage->set($task, $oldStatusId);

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
                'new_status_id' => $statusId,
                'old_status_id' => $oldStatusId,
            ]
        );

        return true;
    }

    /**
     * @param int $id
     * @return Task|null
     */
    public function getTaskById(int $id): ?Task
    {
        $task = $this->taskStorage->load($id);
        if ($task === null) {
            $this->logger->error("task not found by id",
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
    public function deleteTask(Task $task): bool
    {
        $result = $this->taskStorage->delete($task);

        if ($result === false) {
            $this->logger->error("error on delete task",
                [
                    'task_id' => $task->getId(),
                ]
            );
            return false;
        }
        $this->logger->info("deleted task",
            [
                'task_id' => $task->getId(),
            ]
        );
        return true;
    }

    /**
     * @param int $id
     * @return Board|null
     */
    public function getBoardById(int $id): ?Board
    {
        $board = $this->boardStorage->load($id);

        if ($board === null) {
            $this->logger->error("board not found by id",
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

    /**
     * @param Board $board
     * @param string $title
     * @return bool
     */
    public function changeBoard(Board $board, string $title): bool
    {
        $oldTitle = $board->getTitle();

        $board->setTitle($title);

        $result = $this->boardStorage->set($board);

        if (!$result) {
            $this->logger->error("failed update board",
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
        $result = $this->boardStorage->delete($board);

        if ($result === false) {
            $this->logger->error("error on delete board",
                [
                    'board_id' => $board->getId(),
                ]
            );
            return false;
        }
        $this->logger->info("deleted task",
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
        $status = $this->statusStorage->load($id);

        if ($status === null) {
            $this->logger->error("status not found by id",
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

        $result = $this->statusStorage->set($status);

        if (!$result) {
            $this->logger->error("failed update status",
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
        $result = $this->statusStorage->delete($status);

        if ($result === false) {
            $this->logger->error("error on delete status",
                [
                    'status_id' => $status->getId(),
                ]
            );
            return false;
        }
        $this->logger->info("deleted status",
            [
                'status_id' => $status->getId(),
            ]
        );
        return true;
    }

    /**
     * @return Board[]
     */
    public function getBoardList(): array
    {
        $result = $this->boardStorage->all();

        $this->logger->info("get all board",
            [
                'board_count' => count($result),
            ]
        );

        return $result;
    }

    /**
     * @return Status[]
     */
    public function getStatusList(): array
    {
        $result = $this->statusStorage->all();

        $this->logger->info("get all status",
            [
                'status_count' => count($result),
            ]
        );

        return $result;
    }

    /**
     * @param int $boardId
     * @return Status[]
     */
    public function getStatusListByBoardId(int $boardId): array
    {
        $board = $this->boardStorage->load($boardId);
        if ($board === null) {
            $this->logger->error("board not found",
                [
                    'board_id' => $boardId,
                ]);
            return [];
        }
        $result = $this->statusStorage->allByBoardId($boardId);

        $this->logger->info("get all status",
            [
                'status_count' => count($result),
                'board_id' => $boardId,
            ]
        );

        return $result;
    }

    /**
     * @return Task[]
     */
    public function getTaskList(): array
    {
        $result = $this->taskStorage->all();

        $this->logger->info("get all status",
            [
                'task_count' => count($result),
            ]
        );

        return $result;
    }

    /**
     * @param int $boardId
     * @return Task[]
     */
    public function getTaskListByBoardId(int $boardId): array
    {
        $board = $this->boardStorage->load($boardId);
        if ($board === null) {
            $this->logger->error("board not found",
                [
                    'board_id' => $boardId,
                ]);
            return [];
        }
        $result = $this->taskStorage->allByBoardId($boardId);

        $this->logger->info("get all status",
            [
                'task_count' => count($result),
                'board_id' => $boardId,
            ]
        );

        return $result;
    }

    /**
     * @param int $statusId
     * @return Task[]
     */
    public function getTaskListByStatusId(int $statusId): array
    {
        $status = $this->statusStorage->load($statusId);
        if ($status === null) {
            $this->logger->error("board not found",
                [
                    'status_id' => $statusId,
                ]);
            return [];
        }
        $result = $this->taskStorage->allByStatusId($statusId);

        $this->logger->info("get all status",
            [
                'task_count' => count($result),
                'status_id' => $statusId,
            ]
        );

        return $result;
    }
}