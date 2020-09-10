<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Board;

use Gumeniukcom\AbstractService\InMemoryStorageTrait;
use Psr\Log\LoggerInterface;

class BoardInMemoryStorage implements BoardStorage
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    use InMemoryStorageTrait;

    /**
     * BoardInMemoryStorage constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param int $id
     * @return Board|null
     */
    public function load(int $id): ?Board
    {
        $key = self::key($id);
        if (!isset($this->storage[$key])) {
            return null;
        }
        return $this->storage[$key];
    }

    /**
     * @param Board $board
     * @return bool
     */
    public function set(Board $board): bool
    {
        $key = self::key($board->getId());
        if (!isset($this->storage[$key])) {
            return false;
        }

        $this->storage[$key] = $board;

        return true;
    }

    /**
     * @param Board $board
     * @return bool
     */
    public function delete(Board $board): bool
    {
        $key = self::key($board->getId());
        if (!isset($this->storage[$key])) {
            return false;
        }

        unset($this->storage[$key]);

        return true;
    }

    /**
     * @param string $title
     * @return Board|null
     */
    public function new(string $title): ?Board
    {
        $this->storage[self::key(count($this->storage) + 1)] = new Board(count($this->storage) + 1, $title);

        return end($this->storage);
    }

    /**
     * @return Board[]
     */
    public function all(): array
    {
        return $this->storage;
    }
}