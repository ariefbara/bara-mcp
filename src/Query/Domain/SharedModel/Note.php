<?php

namespace Query\Domain\SharedModel;

use DateTimeImmutable;
use SharedContext\Domain\ValueObject\Label;

class Note
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Label
     */
    protected $label;

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

    public function getLabel(): Label
    {
        return $this->label;
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
