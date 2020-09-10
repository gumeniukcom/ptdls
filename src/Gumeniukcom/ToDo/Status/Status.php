<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Status;


use Gumeniukcom\AbstractService\AbstractIdTitleClass;
use Gumeniukcom\ToDo\Board\Board;
use JsonSerializable;

final class Status extends AbstractIdTitleClass implements JsonSerializable
{

    const FIELD_BOARD_ID = 'board_id';

    /** @var int */
    private int $boardId;

    public function __construct(int $id, string $title, int $boardId)
    {
        $this->boardId = $boardId;
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
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            self::FIELD_ID => $this->getId(),
            self::FIELD_TITLE => $this->getTitle(),
            self::FIELD_BOARD_ID => $this->getBoardId(),
        ];
    }

    /**
     * @param array $arr
     * @return Status|null
     */
    public static function fromArray(array $arr): ?Status
    {
        if (count($arr) === 0) {
            return null;
        }

        return new Status((int)$arr[Status::FIELD_ID], $arr[Status::FIELD_TITLE], (int)$arr[Status::FIELD_BOARD_ID]);
    }
}