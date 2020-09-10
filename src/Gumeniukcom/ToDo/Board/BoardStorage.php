<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Board;


interface BoardStorage
{
    /**
     * @param int $id
     * @return Board|null
     */
    public function load(int $id): ?Board;

    /**
     * @param Board $board
     * @return bool
     */
    public function set(Board $board): bool;

    /**
     * @param Board $board
     * @return bool
     */
    public function delete(Board $board): bool;

    /**
     * @param string $title
     * @return Board|null
     */
    public function new(string $title): ?Board;

    /**
     * @return Board[]
     */
    public function all() : array;
}