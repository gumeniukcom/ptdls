<?php declare(strict_types=1);


namespace Gumeniukcom\ToDo\Board;


use Gumeniukcom\AbstractService\AbstractIdTitleClass;
use JsonSerializable;

final class Board extends AbstractIdTitleClass implements JsonSerializable
{
    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}