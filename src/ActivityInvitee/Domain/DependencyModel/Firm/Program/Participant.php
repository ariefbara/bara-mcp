<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Program;

class Participant
{

    /**
     *
     * @var string
     */
    protected $programId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $active = true;

    protected function __construct()
    {
        
    }

}
