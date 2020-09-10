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
    public function load(int $id): ?Task
    {
        $key = self::key($id);
        if (!isset($this->storage[$key])) {
            return null;
        }
        return $this->storage[$key];
    }

    /**
     * @param Task $task
     * @param int|null $oldStatusId
     * @return bool
     */
    public function set(Task $task, ?int $oldStatusId = null): bool
    {
        $key = self::key($task->getId());
        if (!isset($this->storage[$key])) {
            return false;
        }

        $this->storage[$key] = $task;

        return true;
    }

    /**
     * @param Task $entity
     * @return bool
     */
    public function delete(Task $entity): bool
    {
        $key = self::key($entity->getId());
        if (!isset($this->storage[$key])) {
            return false;
        }

        unset($this->storage[$key]);

        return true;
    }

    /**
     * @param string $title
     * @param int $boardId
     * @param int $statusId
     * @return Task|null
     */
    public function new(string $title, int $boardId, int $statusId): ?Task
    {
        $createdAt = new DateTimeImmutable();
        $this->storage[self::key(count($this->storage) + 1)] = new Task(count($this->storage) + 1, $title, $boardId, $statusId, $createdAt);

        return end($this->storage);
    }

    /**
     * @return Task[]
     */
    public function all(): array
    {
        return $this->storage;
    }

    /**
     * @param int $boardId
     * @return Task[]
     */
    public function allByBoardId(int $boardId): array
    {
        $res = [];
        /** @var Task $item */
        foreach ($this->storage as $item) {
            if ($item->getBoardId() == $boardId) {
                $res[] = $item;
            }
        }

        return $res;
    }

    /**
     * @param int $statusId
     * @return Task[]
     */
    public function allByStatusId(int $statusId): array
    {
        $res = [];
        /** @var Task $item */
        foreach ($this->storage as $item) {
            if ($item->getStatusId() == $statusId) {
                $res[] = $item;
            }
        }

        return $res;
    }
}