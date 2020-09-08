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

    public function Load(int $id): ?Board
    {
        $key = self::key($id);
        if (!isset($this->storage[$key])) {
            return null;
        }
        return $this->storage[$key];
    }

    public function Set(Board $board): bool
    {
        $key = self::key($board->getId());
        if (!isset($this->storage[$key])) {
            return false;
        }

        $this->storage[$key] = $board;

        return true;
    }

    public function Delete(Board $board): bool
    {
        $key = self::key($board->getId());
        if (!isset($this->storage[$key])) {
            return false;
        }

        unset($this->storage[$key]);

        return true;
    }

    public function New(string $title): ?Board
    {
        $this->storage[self::key(count($this->storage) + 1)] = new Board(count($this->storage) + 1, $title);

        return end($this->storage);
    }

}