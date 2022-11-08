<?php

namespace Query\Domain\SharedModel;

use DateTimeImmutable;

class Note
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var string
     */
    protected $content;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $createdTime;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $modifiedTime;

    /**
     * 
     * @var bool
     */
    protected $removed;

    protected function __construct()
    {
        
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedTime(): DateTimeImmutable
    {
        return $this->createdTime;
    }

    public function getModifiedTime(): DateTimeImmutable
    {
        return $this->modifiedTime;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

}
