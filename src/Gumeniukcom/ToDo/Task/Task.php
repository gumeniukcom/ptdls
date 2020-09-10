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
    const FIELD_BOARD_ID = 'board_id';
    const FIELD_STATUS_ID = 'status_id';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';

    /** @var int */
    private int $boardId;

    /** @var int */
    private int $statusId;

    /** @var DateTimeImmutable */
    private DateTimeImmutable $createdAt;

    /** @var DateTime|null */
    private ?DateTime $updatedAt;


    /**
     * Task constructor.
     * @param int $id
     * @param string $title
     * @param int $boardId
     * @param int $statusId
     * @param DateTimeImmutable $createdAt
     * @param DateTime|null $updatedAt
     */
    public function __construct(int $id, string $title, int $boardId, int $statusId, DateTimeImmutable $createdAt, ?DateTime $updatedAt = null)
    {

        $this->boardId = $boardId;
        $this->statusId = $statusId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;

        parent::__construct($id, $title);
    }

    /**
     * @return int
     */
    public function getBoardId(): int
    {
        return $this->boardId;
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
     * @param int $statusId
     */
    public function setStatusId(int $statusId): void
    {
        $this->statusId = $statusId;
    }

    /**
     * @param DateTime|null $updatedAt
     */
    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getStatusId(): int
    {
        return $this->statusId;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = [
            self::FIELD_ID => $this->getId(),
            self::FIELD_TITLE => $this->getTitle(),
            self::FIELD_BOARD_ID => $this->getBoardId(),
            self::FIELD_STATUS_ID => $this->getStatusId(),
            self::FIELD_CREATED_AT => $this->getCreatedAt()->format(DATE_ISO8601),
        ];

        if ($this->getUpdatedAt() !== null) {
            $data[self::FIELD_UPDATED_AT] = $this->getUpdatedAt()->format(DATE_ISO8601);
        }

        return $data;
    }

}