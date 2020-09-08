<?php declare(strict_types=1);


namespace Gumeniukcom\AbstractService;


trait InMemoryStorageTrait
{
    /** @var array  */
    protected array $storage = [];

    protected static function key(int $id): string
    {
        return "o_" . $id;
    }
}