<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Status;

use Gumeniukcom\AbstractService\InMemoryStorageTrait;
use Gumeniukcom\ToDo\Board\Board;
use Psr\Log\LoggerInterface;

class StatusInMemoryStorage implements StatusStorage
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    use InMemoryStorageTrait;

    /**
     * StatusInMemoryStorage constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param int $id
     * @return Status|null
     */
    public function load(int $id): ?Status
    {
        $key = self::key($id);
        if (!isset($this->storage[$key])) {
            return null;
        }
        return $this->storage[$key];
    }

    /**
     * @param Status $entity
     * @return bool
     */
    public function set(Status $entity): bool
    {
        $key = self::key($entity->getId());
        if (!isset($this->storage[$key])) {
            return false;
        }

        $this->storage[$key] = $entity;

        return true;
    }

    /**
     * @param Status $entity
     * @return bool
     */
    public function delete(Status $entity): bool
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
     * @return Status|null
     */
    public function new(string $title, int $boardId): ?Status
    {
        $this->storage[self::key(count($this->storage) + 1)] = new Status(count($this->storage,) + 1, $title, $boardId);

        return end($this->storage);
    }

    /**
     * @param int $boardId
     * @return Status[]
     */
    public function allByBoardId(int $boardId): array
    {
        $res = [];
        /** @var Status $item */
        foreach ($this->storage as $item) {
            if ($item->getBoardId() == $boardId) {
                $res[] = $item;
            }
        }

        return $res;
    }

    /**
     * @return Status[]
     */
    public function all(): array
    {
        return $this->storage;
    }
}