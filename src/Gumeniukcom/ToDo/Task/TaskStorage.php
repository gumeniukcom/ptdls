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
     * @return bool
     */
    public function set(Task $task): bool;

    /**
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool;

    /**
     * @param string $title
     * @param int $boardId
     * @param int $statusId
     * @param DateTimeImmutable $createdAt
     * @param DateTime|null $updatedAt
     * @return Task|null
     */
    public function new(string $title, int $boardId, int $statusId, DateTimeImmutable $createdAt, ?DateTime $updatedAt = null): ?Task;

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