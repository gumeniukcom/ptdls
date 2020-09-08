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
    public function CreateTask(string $title, Board $board, Status $status): ?Task;

    /**
     * @param Task $task
     * @param Status $status
     * @return bool
     */
    public function ChangeTaskStatus(Task $task, Status $status): bool;

    /**
     * @param Task $task
     * @param string $title
     * @return bool
     */
    public function ChangeTask(Task $task, string $title): bool;

    /**
     * @param int $id
     * @return Task|null
     */
    public function GetTaskById(int $id): ?Task;

    /**
     * @param Task $task
     * @return bool
     */
    public function DeleteTask(Task $task): bool;
}