<?php

namespace ActivityInvitee\Application\Service\UserParticipant;

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

    public function execute(string $userId, string $invitationId, FormRecordData $formRecordData): void
    {
        $this->activityInvitationRepository->anInvitationBelongsToUser($userId, $invitationId)
                ->submitReport($formRecordData);
        $this->activityInvitationRepository->update();
    }

}
