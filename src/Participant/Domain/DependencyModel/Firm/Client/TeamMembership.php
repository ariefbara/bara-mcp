<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use Participant\Domain\ {
    DependencyModel\Firm\Client,
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Team,
    Model\TeamProgramRegistration
};
use Resources\Exception\RegularException;

class TeamMembership
{

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Team
     */
    protected $team;

    /**
     *
     * @var bool
     */
    protected $active;

    protected function __construct()
    {
        
    }
    
    public function registerTeamToProgram(string $teamProgramRegistrationId, Program $program): TeamProgramRegistration
    {
        $this->assertActive();
        return $this->team->registerToProgram($teamProgramRegistrationId, $program);
    }
    
    public function cancelTeamprogramRegistration(TeamProgramRegistration $teamProgramRegistration): void
    {
        $this->assertActive();
        if (!$teamProgramRegistration->teamEquals($this->team)) {
            $errorDetail = "forbidden: unable to alter registration from other team";
            throw RegularException::forbidden($errorDetail);
        }
        $teamProgramRegistration->cancel();
    }
    
    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active team member can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
