<?php

namespace Firm\Domain\Model;

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

    protected function __construct()
    {
        
    }

}
