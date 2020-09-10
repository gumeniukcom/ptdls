<?php


namespace Gumeniukcom\ToDo\Status;


use Gumeniukcom\AbstractService\RedisStorageTrait;
use Gumeniukcom\ToDo\Board\Board;
use Psr\Log\LoggerInterface;
use Redis;

class StatusRedisStorage implements StatusStorage
{

    const STATUS_ID = "statusid";
    const STATUS_SET = "statusset";
    const STATUS_SET_VIA_BOARD = "status_set_via_board";

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    use RedisStorageTrait;

    /**
     * BoardInMemoryStorage constructor.
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
     * @return Status|null
     */
    public function load(int $id): ?Status
    {
        $key = self::key($id);

        $res = $this->redis->hGetAll($key);

        return Status::fromArray($res);
    }

    /**
     * @param Status $status
     * @return bool
     */
    public function set(Status $status): bool
    {
        $statusJson = $status->jsonSerialize();
        return $this->redis->hMSet(self::key($status->getId()), $statusJson);
    }

    /**
     * @param Status $status
     * @return bool
     */
    public function delete(Status $status): bool
    {
        $key = self::key($status->getId());
        $res = $this->redis->del($key);
        if ($res === 0) {
            $this->logger->debug("status not found on redis", ['status_id' => $status->getId()]);
            return false;
        }
        $this->redis->sRem(self::STATUS_SET, $key);
        $this->redis->sRem(self::keyStatusSettViaBoard($status->getBoardId()), $key);

        return true;
    }


    /**
     * @param string $title
     * @param int $boardId
     * @return Status|null
     */
    public function new(string $title, int $boardId): ?Status
    {
        $newStatusId = $this->redis->incr(self::STATUS_ID);

        $status = new Status($newStatusId, $title, $boardId);

        $statusJson = $status->jsonSerialize();

        $this->redis->sAdd(self::STATUS_SET, self::key($newStatusId));
        $this->redis->sAdd(self::keyStatusSettViaBoard($boardId), self::key($newStatusId));

        $this->redis->hMSet(self::key($newStatusId), $statusJson);

        return $status;
    }


    /**
     * @return Status[]
     */
    public function all(): array
    {
        /** @var Status[] $board */
        $board = [];
        $members = $this->redis->sMembers(self::STATUS_SET);
        foreach ($members as $member) {
//            $id = explode(":", $member);
            $res = $this->redis->hGetAll($member);
            $board[] = Status::fromArray($res);
        }

        return $board;
    }

    /**
     * @param int $boardId
     * @return Status[]
     */
    public function allByBoardId(int $boardId): array
    {
        /** @var Status[] $board */
        $board = [];
        $members = $this->redis->sMembers(self::keyStatusSettViaBoard($boardId));
        foreach ($members as $member) {
//            $id = explode(":", $member);
            $res = $this->redis->hGetAll($member);
            $board[] = Status::fromArray($res);
        }

        return $board;
    }

    protected static function key(int $id): string
    {
        return "status:" . $id;
    }

    /**
     * @param int $boardId
     * @return string
     */
    protected static function keyStatusSettViaBoard(int $boardId): string
    {
        return self::STATUS_SET_VIA_BOARD . ':' . $boardId;
    }

}