<?php declare(strict_types=1);


namespace Gumeniukcom\Tasker;


use Gumeniukcom\ToDo\Board\Board;
use Gumeniukcom\ToDo\Status\Status;
use Gumeniukcom\ToDo\Task\Task;

interface StatusCRUDInterface
{

    /**
     * @param string $title
     * @param Board $board
     * @return Status|null
     */
    public function createStatus(string $title, Board $board): ?Status;

    /**
     * @param int $id
     * @return Board|null
     */
    public function getStatusById(int $id): ?Status;

    /**
     * @param Status $status
     * @param string $title
     * @return bool
     */
    public function changeStatus(Status $status, string $title): bool;

    /**
     * @param Board $stat
     * @return bool
     */
    public function deleteStatus(Status $stat): bool;
}