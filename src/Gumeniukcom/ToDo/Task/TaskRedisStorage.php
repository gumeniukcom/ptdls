<?php


namespace Gumeniukcom\ToDo\Task;


use DateTimeImmutable;
use Exception;
use Gumeniukcom\AbstractService\RedisStorageTrait;
use Psr\Log\LoggerInterface;
use Redis;

class TaskRedisStorage implements TaskStorage
{

    const TASK_ID = "taskid";
    const TASK_SET = "taskset";
    const TASK_SET_VIA_BOARD = "task_set_via_board";
    const TASK_SET_VIA_STATUS = "task_set_via_status";

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    use RedisStorageTrait;

    /**
     * TaskRedisStorage constructor.
     * @param LoggerInterface $logger
     * @param Redis $redis
     */
    public function __construct(LoggerInterface $logger, Redis $redis)
    {
        $this->logger = $logger;
        $this->redis = $redis;
    }


    /**
     * @param int $id
     * @return Task|null
     */
    public function load(int $id): ?Task
    {
        $key = self::key($id);

        $res = $this->redis->hGetAll($key);

        try {
            $task = Task::fromArray($res);
        } catch (Exception $e) {
            $this->logger->error("error on from array task",
                [
                    'e' => $e,
                    'task_id' => $id,
                ]);
            return null;
        }
        return $task;
    }

    /**
     * @param Task $task
     * @param int|null $oldStatusId
     * @return bool
     */
    public function set(Task $task, ?int $oldStatusId = null): bool
    {

        $key = self::key($task->getId());
        $statusJson = $task->jsonSerialize();
        $result = $this->redis->hMSet(self::key($task->getId()), $statusJson);
        if (!$result) {
            return false;
        }
        if ($oldStatusId === null || $oldStatusId === $task->getStatusId()) {
            return true;
        }
        $this->redis->sRem(self::keyTaskSettViaStatus($oldStatusId), $key);
        $this->redis->sAdd(self::keyTaskSettViaStatus($task->getStatusId()), $key);

        return true;
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool
    {
        $key = self::key($task->getId());
        $res = $this->redis->del($key);
        if ($res === 0) {
            $this->logger->debug("status not found on redis", ['status_id' => $task->getId()]);
            return false;
        }
        $this->redis->sRem(self::TASK_SET, $key);
        $this->redis->sRem(self::keyTaskSettViaBoard($task->getBoardId()), $key);
        $this->redis->sRem(self::keyTaskSettViaStatus($task->getStatusId()), $key);

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
        $newStatusId = $this->redis->incr(self::TASK_ID);

        $createdAt = new DateTimeImmutable();
        $status = new Task($newStatusId, $title, $boardId, $statusId, $createdAt);

        $statusJson = $status->jsonSerialize();

        $this->redis->sAdd(self::TASK_SET, self::key($newStatusId));
        $this->redis->sAdd(self::keyTaskSettViaBoard($boardId), self::key($newStatusId));
        $this->redis->sAdd(self::keyTaskSettViaStatus($statusId), self::key($newStatusId));

        $this->redis->hMSet(self::key($newStatusId), $statusJson);

        return $status;
    }


    /**
     * @return Task[]
     */
    public function all(): array
    {
        /** @var Task[] $board */
        $board = [];
        $members = $this->redis->sMembers(self::TASK_SET);
        foreach ($members as $member) {
//            $id = explode(":", $member);
            $res = $this->redis->hGetAll($member);
            $board[] = Task::fromArray($res);
        }

        return $board;
    }

    /**
     * @param int $boardId
     * @return Task[]
     */
    public function allByBoardId(int $boardId): array
    {
        /** @var Task[] $tasks */
        $tasks = [];
        $members = $this->redis->sMembers(self::keyTaskSettViaBoard($boardId));
        foreach ($members as $member) {
//            $id = explode(":", $member);
            $res = $this->redis->hGetAll($member);
            $tasks[] = Task::fromArray($res);
        }

        return $tasks;
    }

    protected static function key(int $id): string
    {
        return "task:" . $id;
    }

    /**
     * @param int $boardId
     * @return string
     */
    protected static function keyTaskSettViaBoard(int $boardId): string
    {
        return self::TASK_SET_VIA_BOARD . ':' . $boardId;
    }

    /**
     * @param int $statusId
     * @return string
     */
    protected static function keyTaskSettViaStatus(int $statusId): string
    {
        return self::TASK_SET_VIA_STATUS . ':' . $statusId;
    }

    /**
     * @param int $statusId
     * @return Task[]
     */
    public function allByStatusId(int $statusId): array
    {
        /** @var Task[] $tasks */
        $tasks = [];
        $members = $this->redis->sMembers(self::keyTaskSettViaStatus($statusId));
        foreach ($members as $member) {
//            $id = explode(":", $member);
            $res = $this->redis->hGetAll($member);
            $tasks[] = Task::fromArray($res);
        }

        return $tasks;
    }
}