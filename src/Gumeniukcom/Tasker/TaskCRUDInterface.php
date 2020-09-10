<?php declare(strict_types=1);


namespace Gumeniukcom\Tasker;


use Gumeniukcom\ToDo\Board\Board;
use Gumeniukcom\ToDo\Status\Status;
use Gumeniukcom\ToDo\Task\Task;

interface TaskCRUDInterface
{
    /**
     * @param string $title
     * @param Board $board
     * @param Status $status
     * @return Task|null
     */
    public function createTask(string $title, Board $board, Status $status): ?Task;

    /**
     * @param Task $task
     * @param Status $status
     * @return bool
     */
    public function changeTaskStatus(Task $task, Status $status): bool;

    /**
     * @param Task $task
     * @param string|null $title
     * @param int|null $statusId
     * @return bool
     */
    public function changeTask(Task $task, ?string $title = null, ?int $statusId = null): bool;

    /**
     * @param int $id
     * @return Task|null
     */
    public function getTaskById(int $id): ?Task;

    /**
     * @param Task $task
     * @return bool
     */
    public function deleteTask(Task $task): bool;

    /**
     * @return Task[]
     */
    public function getTaskList(): array;

    /**
     * @param int $boardId
     * @return Task[]
     */
    public function getTaskListByBoardId(int $boardId): array;
    /**
     * @param int $statusId
     * @return Task[]
     */
    public function getTaskListByStatusId(int $statusId): array;
}