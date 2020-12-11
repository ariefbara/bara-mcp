<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\RegistrantProfileRepository;

class RemoveRegistrantProfile
{

    /**
     * 
     * @var RegistrantProfileRepository
     */
    protected $registrantProfileRepository;

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var TeamRegistrantRepository
     */
    protected $teamRegistrantRepository;

    function __construct(
            RegistrantProfileRepository $registrantProfileRepository, TeamMemberRepository $teamMemberRepository,
            TeamRegistrantRepository $teamRegistrantRepository)
    {
        $this->registrantProfileRepository = $registrantProfileRepository;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamRegistrantRepository = $teamRegistrantRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $programRegistrationId,
            string $programsProfileFormId): void
    {
        $programRegistration = $this->teamRegistrantRepository->ofId($programRegistrationId);
        $registrantProfile = $this->registrantProfileRepository
                ->aRegistrantProfileCorrespondWithProgramsProfileForm($programRegistrationId, $programsProfileFormId);

        $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->removeRegistrantProfile($programRegistration, $registrantProfile);
        
        $this->registrantProfileRepository->update();
    }

}
