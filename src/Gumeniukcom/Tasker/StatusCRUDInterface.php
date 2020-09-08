<?php declare(strict_types=1);


namespace Gumeniukcom\Tasker;


use Gumeniukcom\ToDo\Board\Board;
use Gumeniukcom\ToDo\Status\Status;
use Gumeniukcom\ToDo\Task\Task;

interface StatusCRUDInterface
{

    /**
     * @param string $title
     * @return Status|null
     */
    public function CreateStatus(string $title): ?Status;
}