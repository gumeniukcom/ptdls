<?php declare(strict_types=1);


namespace Gumeniukcom\Tasker;


use Gumeniukcom\ToDo\Board\Board;
use Gumeniukcom\ToDo\Status\Status;
use Gumeniukcom\ToDo\Task\Task;

interface BoardCRUDInterface
{
    /**
     * @param string $title
     * @return Board|null
     */
    public function createBoard(string $title): ?Board;

    /**
     * @param int $id
     * @return Board|null
     */
    public function getBoardById(int $id): ?Board;

    /**
     * @param Board $board
     * @param string $title
     * @return bool
     */
    public function changeBoard(Board $board, string $title): bool;

    /**
     * @param Board $board
     * @return bool
     */
    public function deleteBoard(Board $board): bool;
}