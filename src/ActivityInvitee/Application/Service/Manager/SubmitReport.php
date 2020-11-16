<?php

namespace ActivityInvitee\Application\Service\Manager;

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
    
    public function execute(string $firmId, string $managerId, string $invitationId, FormRecordData $formRecordData): void
    {
        $this->activityInvitationRepository->anInvitationBelongsToManager($firmId, $managerId, $invitationId)
                ->submitReport($formRecordData);
        $this->activityInvitationRepository->update();
    }

}
