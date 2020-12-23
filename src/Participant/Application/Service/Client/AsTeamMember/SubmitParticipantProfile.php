<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\ProgramsProfileFormRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitParticipantProfile
{
    /**
     * 
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

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
    
    function __construct(TeamParticipantRepository $teamParticipantRepository,
            TeamMemberRepository $teamMemberRepository, ProgramsProfileFormRepository $programsProfileFormRepository)
    {
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->programsProfileFormRepository = $programsProfileFormRepository;
    }
    
    public function execute(
            string $firmId, string $clientId, string $teamId, string $programParticipationId,
            string $programsProfileFormId, FormRecordData $formRecordData)
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($programParticipationId);
        $programsProfileForm = $this->programsProfileFormRepository->ofId($programsProfileFormId);
        $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->submitParticipantProfile($teamParticipant, $programsProfileForm, $formRecordData);
        
        $this->teamParticipantRepository->update();
    }

}
