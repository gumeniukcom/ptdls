<?php declare(strict_types=1);


namespace Gumeniukcom\AbstractService;


use Redis;

trait RedisStorageTrait
{
    /** @var Redis */
    protected Redis $redis;
}