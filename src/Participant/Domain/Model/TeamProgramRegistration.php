<?php

namespace Participant\Domain\Model;

use Participant\Domain\DependencyModel\Firm\ {
    Client\AssetBelongsToTeamInterface,
    Program,
    Team
};
use Query\Domain\Model\Firm\ParticipantTypes;
use Resources\Exception\RegularException;

class TeamProgramRegistration implements AssetBelongsToTeamInterface
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
     * @var ProgramRegistration
     */
    protected $programRegistration;

    public function __construct(Team $team, string $id, Program $program)
    {
        if (!$program->isRegistrationOpenFor(ParticipantTypes::TEAM_TYPE)) {
            $errorDetail = "forbidden: program registration is closed or unavailable for team";
            throw RegularException::forbidden($errorDetail);
        }
        
        $this->team = $team;
        $this->id = $id;
        $this->programRegistration = new ProgramRegistration($program, $id);
    }

    public function cancel(): void
    {
        $this->programRegistration->cancel();
    }

    public function isUnconcludedRegistrationToProgram(Program $program): bool
    {
        return $this->programRegistration->isUnconcludedRegistrationToProgram($program);
    }

    public function belongsToTeam(Team $team): bool
    {
        return $this->team === $team;
    }

}
