<?php

namespace Personnel\Domain\Model\Firm\Program\Participant;

use Personnel\Domain\Model\Firm\Program\Participant;

class Worksheet
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var string
     */
    protected $id;
    /**
     *
     * @var bool
     */
    protected $removed;
    
    protected function __construct()
    {
        ;
    }
}
