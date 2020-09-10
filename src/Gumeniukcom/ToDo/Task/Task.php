<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Task;


use DateTimeImmutable;
use DateTime;
use Gumeniukcom\AbstractService\AbstractIdTitleClass;
use Gumeniukcom\ToDo\Board\Board;
use Gumeniukcom\ToDo\Status\Status;
use JsonSerializable;

final class Task extends AbstractIdTitleClass implements JsonSerializable
{

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

        $this->board = $board;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;

        parent::__construct($id, $title);
    }

    /**
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
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
     * @param Status $status
     */
    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    /**
     * @param DateTime|null $updatedAt
     */
    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'board_id' => $this->getBoard()->getId(),
            'status_id' => $this->getStatus()->getId(),
            'created_at' => $this->getCreatedAt()->format(DATE_ISO8601),
        ];

        if ($this->getUpdatedAt() !== null) {
            $data['updated_at'] = $this->getUpdatedAt()->format(DATE_ISO8601);
        }

        return $data;
    }

}