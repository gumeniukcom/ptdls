<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Status;


use Gumeniukcom\ToDo\Board\Board;

interface StatusStorage
{
    /**
     * @param int $id
     * @return Status|null
     */
    public function Load(int $id): ?Status;


    /**
     * @param Status $status
     * @return bool
     */
    public function Set(Status $status): bool;

    /**
     * @param Status $status
     * @return bool
     */
    public function Delete(Status $status): bool;

    /**
     * @param string $title
     * @param Board $board
     * @return Status|null
     */
    public function New(string $title, Board $board): ?Status;
}