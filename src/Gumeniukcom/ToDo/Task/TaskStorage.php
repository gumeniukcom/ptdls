<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Task;



use DateTime;
use DateTimeImmutable;
use Gumeniukcom\ToDo\Board\Board;
use Gumeniukcom\ToDo\Status\Status;

interface TaskStorage
{
    /**
     * @param int $id
     * @return Task|null
     */
    public function Load(int $id): ?Task;


    /**
     * @param Task $task
     * @return bool
     */
    public function Set(Task $task): bool;

    /**
     * @param Task $task
     * @return bool
     */
    public function Delete(Task $task): bool;

    /**
     * @param string $title
     * @param Board $board
     * @param Status $status
     * @param DateTimeImmutable $createdAt
     * @param DateTime|null $updatedAt
     * @return Task|null
     */
    public function New(string $title, Board $board, Status $status, DateTimeImmutable $createdAt, ?DateTime $updatedAt = null): ?Task;
}