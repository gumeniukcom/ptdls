<?php declare(strict_types=1);


namespace Gumeniukcom\AbstractService;


abstract class AbstractIdTitleClass
{

    const FIELD_ID = 'id';
    const FIELD_TITLE = 'title';

    /** @var int */
    protected int $id;

    /** @var string */
    protected string $title;

    /**
     * @param int $id
     * @param string $title
     */
    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            self::FIELD_ID => $this->getId(),
            self::FIELD_TITLE => $this->getTitle(),
        ];
    }
}