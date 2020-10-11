<?php

namespace Firm\Domain\Model;

use Query\Domain\Model\FirmWhitelableInfo;
use Resources\Domain\Model\EntityContainEvents;

class Firm extends EntityContainEvents
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
     * @var FirmWhitelableInfo
     */
    protected $firmWhitelableInfo;

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
