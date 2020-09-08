<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Task;

use DateTimeImmutable;
use DateTime;
use Gumeniukcom\AbstractService\InMemoryStorageTrait;
use Gumeniukcom\ToDo\Board\Board;
use Gumeniukcom\ToDo\Status\Status;
use Psr\Log\LoggerInterface;

class TaskInMemoryStorage implements TaskStorage
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    use InMemoryStorageTrait;

    /**
     * TaskInMemoryStorage constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param int $id
     * @return Task|null
     */
    public function Load(int $id): ?Task
    {
        $key = self::key($id);
        if (!isset($this->storage[$key])) {
            return null;
        }
        return $this->storage[$key];
    }

    /**
     * @param Task $entity
     * @return bool
     */
    public function Set(Task $entity): bool
    {
        $key = self::key($entity->getId());
        if (!isset($this->storage[$key])) {
            return false;
        }

        $this->storage[$key] = $entity;

        return true;
    }

    /**
     * @param Task $entity
     * @return bool
     */
    public function Delete(Task $entity): bool
    {
        $key = self::key($entity->getId());
        if (!isset($this->storage[$key])) {
            return false;
        }

        unset($this->storage[$key]);

        return true;
    }

    /**
     * @param int $id
     * @param string $title
     * @param Board $board
     * @param Status $status
     * @param DateTimeImmutable $createdAt
     * @param DateTime|null $updatedAt
     * @return Task|null
     */
    public function New(int $id, string $title, Board $board, Status $status, DateTimeImmutable $createdAt, ?DateTime $updatedAt = null): ?Task
    {
        $this->storage[self::key(count($this->storage) + 1)] = new Task(count($this->storage) + 1, $title, $board, $status, $createdAt, $updatedAt);

        return end($this->storage);
    }

}