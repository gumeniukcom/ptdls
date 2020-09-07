<?php declare(strict_types=1);


namespace Gumeniukcom\Tasker\Storage;


use Gumeniukcom\Tasker\Objects\Board;

interface BoardStorage
{
    /**
     * @param int $id
     * @return Board|null
     */
    public function Load(int $id): ?Board;

    /**
     * @param Board $board
     * @return bool
     */
    public function Set(Board $board): bool;

    /**
     * @param Board $board
     * @return bool
     */
    public function Delete(Board $board): bool;

    /**
     * @param string $title
     * @return Board|null
     */
    public function New(string $title): ?Board;
}