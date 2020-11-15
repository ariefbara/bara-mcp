<?php

namespace ActivityInvitee\Application\Service\TeamMember;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitReport
{

    /**
     *
     * @var ActivityInvitationRepository
     */
    protected $activityInvitationRepository;

    /**
     *
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    function __construct(ActivityInvitationRepository $activityInvitationRepository,
            TeamMemberRepository $teamMemberRepository)
    {
        $this->activityInvitationRepository = $activityInvitationRepository;
        $this->teamMemberRepository = $teamMemberRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $invitationId, FormRecordData $formRecordData): void
    {
        $invitation = $this->activityInvitationRepository->ofId($invitationId);
        $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->submitInviteeReportIn($invitation, $formRecordData);
        
        $this->activityInvitationRepository->update();
    }

}
