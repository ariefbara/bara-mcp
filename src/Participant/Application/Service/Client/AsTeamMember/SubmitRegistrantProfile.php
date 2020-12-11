<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\ProgramsProfileFormRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitRegistrantProfile
{

    /**
     * 
     * @var TeamRegistrantRepository
     */
    protected $teamRegistrantRepository;

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var ProgramsProfileFormRepository
     */
    protected $programsProfileFormRepository;

    function __construct(
            TeamRegistrantRepository $teamRegistrantRepository, TeamMemberRepository $teamMemberRepository,
            ProgramsProfileFormRepository $programsProfileFormRepository)
    {
        $this->teamRegistrantRepository = $teamRegistrantRepository;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->programsProfileFormRepository = $programsProfileFormRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $programRegistrationId,
            string $programsProfileFormId, FormRecordData $formRecordData): void
    {
        $programRegistration = $this->teamRegistrantRepository->ofId($programRegistrationId);
        $programsProfileForm = $this->programsProfileFormRepository->ofId($programsProfileFormId);
        
        $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->submitRegistrantProfile($programRegistration, $programsProfileForm, $formRecordData);
        
        $this->teamRegistrantRepository->update();
    }

}
