<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Board;


use Gumeniukcom\AbstractService\AbstractIdTitleClass;
use JsonSerializable;

final class Board extends AbstractIdTitleClass implements JsonSerializable
{
    /**
     * @param array $arr
     * @return Board|null
     */
    public static function fromArray(array $arr):?Board {
        if (count($arr) === 0) {
            return null;
        }

        return new Board((int)$arr[Board::FIELD_ID], $arr[Board::FIELD_TITLE]);
    }
}