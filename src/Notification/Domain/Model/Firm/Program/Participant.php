<?php

namespace Notification\Domain\Model\Program;

use Notification\Domain\Model\Firm\Team\TeamProgramParticipation;

class Participant
{
    /**
     *
     * @var Program
     */
    protected $program;
    /**
     *
     * @var string
     */
    protected $id;
    /**
     *
     * @var TeamProgramParticipation
     */
    protected $teamParticipant;
    protected $clientParticipant;
    protected $userParticipant;
    
    protected function __construct()
    {
        ;
    }
}
