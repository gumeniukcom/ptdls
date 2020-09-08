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
    public function CreateBoard(string $title): ?Board;
}