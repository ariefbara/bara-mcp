<?php

namespace ActivityInvitee\Application\Service\Consultant;

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
    
    public function execute(string $firmId, string $personnelId, string $invitationId, FormRecordData $formRecordData): void
    {
        $this->activityInvitationRepository->anInvitationBelongsToPersonnel($firmId, $personnelId, $invitationId)
                ->submitReport($formRecordData);
        $this->activityInvitationRepository->update();
    }

}
