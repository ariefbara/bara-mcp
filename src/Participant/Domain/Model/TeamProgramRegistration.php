<?php

namespace Participant\Domain\Model;

use Participant\Domain\DependencyModel\Firm\Client\AssetBelongsToTeamInterface;
use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\DependencyModel\Firm\Team;
use Participant\Domain\Model\Registrant\RegistrantProfile;
use Query\Domain\Model\Firm\ParticipantTypes;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

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

    public function submitProfile(ProgramsProfileForm $programsProfileForm, FormRecordData $formRecordData): void
    {
        $this->programRegistration->submitProfile($programsProfileForm, $formRecordData);
    }

    public function removeProfile(RegistrantProfile $registrantProfile): void
    {
        $this->programRegistration->removeProfile($registrantProfile);
    }

}
