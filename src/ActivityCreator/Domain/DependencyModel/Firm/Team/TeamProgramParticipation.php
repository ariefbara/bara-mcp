<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Team;

use ActivityCreator\Domain\DependencyModel\Firm\Program\Participant;

class TeamProgramParticipation
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
