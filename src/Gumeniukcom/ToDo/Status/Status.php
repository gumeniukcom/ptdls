<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Status;


use Gumeniukcom\AbstractService\AbstractIdTitleClass;
use Gumeniukcom\ToDo\Board\Board;
use JsonSerializable;

final class Status extends AbstractIdTitleClass implements JsonSerializable
{
    /** @var Board */
    private Board $board;

    public function __construct(int $id, string $title, Board $board)
    {
        $this->board = $board;
        parent::__construct($id, $title);
    }

    /**
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'board_id' => $this->getBoard()->getId(),
        ];
    }

}