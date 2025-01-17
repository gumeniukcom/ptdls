<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Task;

use DateTime;
use DateTimeImmutable;

interface TaskStorage
{
    /**
     * @param int $id
     * @return Task|null
     */
    public function load(int $id): ?Task;


    /**
     * @param Task $task
     * @param int|null $oldStatusId
     * @return bool
     */
    public function set(Task $task, ?int $oldStatusId = null): bool;

    /**
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool;

    /**
     * @param string $title
     * @param int $boardId
     * @param int $statusId
     * @return Task|null
     */
    public function new(string $title, int $boardId, int $statusId): ?Task;

    /**
     * @return Task[]
     */
    public function all(): array;

    /**
     * @param int $boardId
     * @return Task[]
     */
    public function allByBoardId(int $boardId): array;

    /**
     * @param int $statusId
     * @return Task[]
     */
    public function allByStatusId(int $statusId): array;
}