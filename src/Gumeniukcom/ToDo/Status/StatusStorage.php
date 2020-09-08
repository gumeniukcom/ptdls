<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Status;


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
     * @return Status|null
     */
    public function New(string $title): ?Status;
}