<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Team;

use ActivityInvitee\Domain\DependencyModel\Firm\Program\Participant;

class ProgramParticipation
{

    /**
     *
     * @var string
     */
    protected $teamId;

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
