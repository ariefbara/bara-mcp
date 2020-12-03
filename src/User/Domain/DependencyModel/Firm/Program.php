<?php

namespace User\Domain\DependencyModel\Firm;

use Query\Domain\Model\Firm\ParticipantTypes;
use User\Domain\DependencyModel\Firm;

class Program
{
    /**
     *
     * @var Firm
     */
    protected $firm;

    /**
     *
     * @var string
     */
    protected $id;
    
    /**
     *
     * @var ParticipantTypes
     */
    protected $participantTypes;

    /**
     *
     * @var bool
     */
    protected $published;

    /**
     *
     * @var bool
     */
    protected $removed;
    
    protected function __construct()
    {
        
    }
}
