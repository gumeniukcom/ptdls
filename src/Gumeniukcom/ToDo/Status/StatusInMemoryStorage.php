<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Status;

use Gumeniukcom\AbstractService\InMemoryStorageTrait;
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

    public function Load(int $id): ?Status
    {
        $key = self::key($id);
        if (!isset($this->storage[$key])) {
            return null;
        }
        return $this->storage[$key];
    }

    public function Set(Status $entity): bool
    {
        $key = self::key($entity->getId());
        if (!isset($this->storage[$key])) {
            return false;
        }

        $this->storage[$key] = $entity;

        return true;
    }

    public function Delete(Status $entity): bool
    {
        $key = self::key($entity->getId());
        if (!isset($this->storage[$key])) {
            return false;
        }

        unset($this->storage[$key]);

        return true;
    }

    public function New(string $title): ?Status
    {
        $this->storage[self::key(count($this->storage) + 1)] = new Status(count($this->storage) + 1, $title);

        return end($this->storage);
    }

}