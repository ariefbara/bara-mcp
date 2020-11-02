<?php

namespace ActivityCreator\Domain\DependencyModel\User;

use ActivityCreator\Domain\DependencyModel\Firm\Program\Participant;

class UserParticipant
{

    /**
     *
     * @var string
     */
    protected $userId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $participant;

    protected function __construct()
    {
        
    }

}
