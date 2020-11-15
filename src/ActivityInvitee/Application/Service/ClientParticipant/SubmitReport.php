<?php

namespace ActivityInvitee\Application\Service\ClientParticipant;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitReport
{
    /**
     *
     * @var ActivityInvitationRepository
     */
    protected $activityInvitationRepository;
    
    function __construct(ActivityInvitationRepository $activityInvitationRepository)
    {
        $this->activityInvitationRepository = $activityInvitationRepository;
    }
    
    public function execute(string $firmId, string $clientId, string $invitationId, FormRecordData $formRecordData): void
    {
        $this->activityInvitationRepository->anInvitationBelongsToClient($firmId, $clientId, $invitationId)
                ->submitReport($formRecordData);
        $this->activityInvitationRepository->update();
    }
}
