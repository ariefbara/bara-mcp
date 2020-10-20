<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership;

use Participant\Application\Service\ {
    Firm\Client\TeamMembershipRepository,
    Firm\ProgramRepository,
    TeamProgramRegistrationRepository
};

class RegisterTeamToProgram
{

    /**
     *
     * @var TeamProgramRegistrationRepository
     */
    protected $teamProgramRegistrationRepository;

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;

    public function __construct(
            TeamProgramRegistrationRepository $teamProgramRegistrationRepository,
            TeamMembershipRepository $teamMembershipRepository, ProgramRepository $programRepository)
    {
        $this->teamProgramRegistrationRepository = $teamProgramRegistrationRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->programRepository = $programRepository;
    }

    public function execute(string $firmId, string $clientId, string $teamMembershipId, string $programId): string
    {
        $id = $this->teamProgramRegistrationRepository->nextIdentity();
        $program = $this->programRepository->ofId($firmId, $programId);
        
        $teamProgramRegistration = $this->teamMembershipRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamMembershipId)
                ->registerTeamToProgram($id, $program);
        
        $this->teamProgramRegistrationRepository->add($teamProgramRegistration);
        return $id;
    }

}
