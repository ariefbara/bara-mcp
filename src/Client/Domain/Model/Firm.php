<?php

namespace Client\Domain\Model;

class Firm
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
    protected $name;

    /**
     *
     * @var string
     */
    protected $identifier;

    /**
     *
     * @var bool
     */
    protected $suspended = false;

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getIdentifier(): string
    {
        return $this->identifier;
    }

    function isSuspended(): bool
    {
        return $this->suspended;
    }

    protected function __construct()
    {
        
    }

}
