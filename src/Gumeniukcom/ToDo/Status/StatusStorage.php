<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Status;


use Gumeniukcom\ToDo\Board\Board;

interface StatusStorage
{
    /**
     * @param int $id
     * @return Status|null
     */
    public function load(int $id): ?Status;


    /**
     * @param Status $status
     * @return bool
     */
    public function set(Status $status): bool;

    /**
     * @param Status $status
     * @return bool
     */
    public function delete(Status $status): bool;

    /**
     * @param string $title
     * @param int $boardId
     * @return Status|null
     */
    public function new(string $title, int $boardId): ?Status;

    /**
     * @param int $boardId
     * @return Status[]
     */
    public function allByBoardId(int $boardId): array;

    /**
     * @return Status[]
     */
    public function all(): array;
}