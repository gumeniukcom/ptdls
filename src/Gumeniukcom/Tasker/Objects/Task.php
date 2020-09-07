<?php declare(strict_types=1);


namespace Gumeniukcom\Tasker\Objects;


use DateTimeImmutable;
use DateTime;

class Task
{
    /** @var int */
    private int $id;

    /** @var string */
    private string $title;

    /** @var Board */
    private Board $board;

    /** @var Status */
    private Status $status;

    /** @var DateTimeImmutable */
    private DateTimeImmutable $createdAt;

    /** @var DateTime|null */
    private ?DateTime $updatedAt;



    /**
     * Task constructor.
     * @param int $id
     * @param string $title
     * @param Board $board
     * @param Status $status
     * @param DateTimeImmutable $createdAt
     * @param DateTime|null $updatedAt
     */
    public function __construct(int $id, string $title, Board $board, Status $status, DateTimeImmutable $createdAt, ?DateTime $updatedAt = null)
    {

        $this->id = $id;
        $this->title = $title;
        $this->board = $board;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }


}