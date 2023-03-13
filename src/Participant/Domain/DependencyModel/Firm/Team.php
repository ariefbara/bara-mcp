<?php

namespace Participant\Domain\DependencyModel\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\ {
    TeamProgramParticipation,
    TeamProgramRegistration
};
use Resources\Exception\RegularException;

class Team
{

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     *
     * @var ArrayCollection
     */
    protected $teamProgramParticipations;

    /**
     *
     * @var ArrayCollection
     */
    protected $teamProgramRegistrations;

    protected function __construct()
    {
        
    }

//    public function registerToProgram(string $teamProgramRegistrationId, Program $program): TeamProgramRegistration
//    {
//        if (!$program->firmIdEquals($this->firmId)) {
//            $errorDetail = 'forbidden: cannot register to program from different firm';
//            throw RegularException::forbidden($errorDetail);
//        }
//        $this->assertNoUnconcludedRegistrationToSameProgram($program);
//        $this->assertNoActiveParticipationInSameProgram($program);
//        return new TeamProgramRegistration($this, $teamProgramRegistrationId, $program);
//    }
    
    protected function assertNoUnconcludedRegistrationToSameProgram(Program $program): void
    {
        $p = function (TeamProgramRegistration $teamProgramRegistration) use ($program) {
            return $teamProgramRegistration->isUnconcludedRegistrationToProgram($program);
        };
        if (!empty($this->teamProgramRegistrations->filter($p)->count())) {
            $errorDetail = 'forbidden: your team already registered to this program';
            throw RegularException::forbidden($errorDetail);
        }
    }
    protected function assertNoActiveParticipationInSameProgram(Program $program): void
    {
        $p = function (TeamProgramParticipation $teamProgramParticipation) use ($program) {
            return $teamProgramParticipation->isActiveParticipantOfProgram($program);
        };
        if (!empty($this->teamProgramParticipations->filter($p)->count())) {
            $errorDetail = 'forbidden: your team already participante in this program';
            throw RegularException::forbidden($errorDetail);
        }
    }

}
