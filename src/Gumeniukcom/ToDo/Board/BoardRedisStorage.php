<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Board;

use Gumeniukcom\AbstractService\InMemoryStorageTrait;
use Gumeniukcom\AbstractService\RedisStorageTrait;
use Psr\Log\LoggerInterface;
use Redis;

class BoardRedisStorage implements BoardStorage
{

    const BOARD_ID = "boardid";
    const BOARDS_SET = "boardset";

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
     * @return Board|null
     */
    public function load(int $id): ?Board
    {
        $key = self::key($id);

        $res = $this->redis->hGetAll($key);

        return Board::fromArray($res);
    }

    /**
     * @param Board $board
     * @return bool
     */
    public function set(Board $board): bool
    {
        $boardJson = $board->jsonSerialize();
        return $this->redis->hMSet(self::key($board->getId()), $boardJson);
    }

    /**
     * @param Board $board
     * @return bool
     */
    public function delete(Board $board): bool
    {
        $key = self::key($board->getId());
        $res = $this->redis->del($key);
        if ($res === 0) {
            $this->logger->debug("board not found on redis", ['board_id' => $board->getId()]);
            return false;
        }
        $this->redis->sRem(self::BOARDS_SET, $key);

        return true;
    }

    /**
     * @param string $title
     * @return Board|null
     */
    public function new(string $title): ?Board
    {
        $newBoardId = $this->redis->incr(self::BOARD_ID);

        $board = new Board($newBoardId, $title);

        $boardJson = $board->jsonSerialize();

        $this->redis->sAdd(self::BOARDS_SET, self::key($newBoardId));

        $this->redis->hMSet(self::key($newBoardId), $boardJson);

        return $board;
    }




    /**
     * @return Board[]
     */
    public function all(): array
    {
        /** @var Board[] $board */
        $board = [];
        $members = $this->redis->sMembers(self::BOARDS_SET);
        foreach ($members as $member) {
//            $id = explode(":", $member);
            $res = $this->redis->hGetAll($member);
            $board[] = Board::fromArray($res);
        }

        return $board;
    }

    protected static function key(int $id): string
    {
        return "board:" . $id;
    }
}