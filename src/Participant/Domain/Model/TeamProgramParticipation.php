<?php

namespace Participant\Domain\Model;

use Participant\Domain\DependencyModel\Firm\ {
    Program,
    Team
};

class TeamProgramParticipation
{

    /**
     *
     * @var Team
     */
    protected $team;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $programParticipation;

    protected function __construct()
    {
        
    }
    
    public function isActiveParticipantOfProgram(Program $program): bool
    {
        return $this->programParticipation->isActiveParticipantOfProgram($program);
    }

}
