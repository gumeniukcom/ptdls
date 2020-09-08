<?php declare(strict_types=1);


namespace Gumeniukcom\Tasker;



use Gumeniukcom\ToDo\Board\Board;
use Gumeniukcom\ToDo\Board\BoardStorage;
use Gumeniukcom\ToDo\Status\StatusStorage;
use Gumeniukcom\ToDo\Task\TaskStorage;
use Psr\Log\LoggerInterface;

class Tasker implements TaskerInterface
{
    /** @var StatusStorage */
    private StatusStorage $statusStorage;

    /** @var BoardStorage */
    private BoardStorage $boardStorage;

    /** @var TaskStorage */
    private TaskStorage $taskStorage;

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /**
     * Tasker constructor.
     * @param LoggerInterface $logger
     * @param StatusStorage $statusStorage
     * @param BoardStorage $boardStorage
     * @param TaskStorage $taskStorage
     */
    public function __construct(LoggerInterface $logger, StatusStorage $statusStorage, BoardStorage $boardStorage, TaskStorage $taskStorage)
    {
        $this->logger = $logger;
        $this->statusStorage = $statusStorage;
        $this->boardStorage = $boardStorage;
        $this->taskStorage = $taskStorage;
    }

    /**
     * @param string $title
     * @return Board|null
     */
    public function CreateBoard(string $title): ?Board
    {
        $board = $this->boardStorage->New($title);
        if ($board === null) {
            $this->logger->error("error on get new Board",
                ['title' => $title]
            );
            return null;
        }

        $this->logger->debug("new board created",
            ['title' => $title]
        );
        return $board;
    }


}