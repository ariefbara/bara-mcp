<?php

namespace ActivityInvitee\Domain\DependencyModel\User;

use ActivityInvitee\Domain\DependencyModel\Firm\Program\Participant;

class ProgramParticipation
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
