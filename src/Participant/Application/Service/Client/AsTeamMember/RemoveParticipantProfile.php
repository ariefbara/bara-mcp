<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\ParticipantProfileRepository;

class RemoveParticipantProfile
{

    /**
     * 
     * @var ParticipantProfileRepository
     */
    protected $participantProfileRepository;

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

    function __construct(
            ParticipantProfileRepository $participantProfileRepository, TeamMemberRepository $teamMemberRepository,
            TeamParticipantRepository $teamParticipantRepository)
    {
        $this->participantProfileRepository = $participantProfileRepository;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $programParticipationId,
            string $programsProfileFormId)
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($programParticipationId);
        $participantProfile = $this->participantProfileRepository
                ->aParticipantProfileCorrespondWithProgramsProfileForm($programParticipationId, $programsProfileFormId);
        $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->removeParticipantProfile($teamParticipant, $participantProfile);
        $this->participantProfileRepository->update();
    }

}
