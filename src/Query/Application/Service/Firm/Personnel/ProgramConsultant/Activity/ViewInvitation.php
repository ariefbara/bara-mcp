<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant\Activity;

use Query\Domain\Model\Firm\Program\Activity\Invitation;

class ViewInvitation
{

    /**
     *
     * @var InvitationRepository
     */
    protected $invitationRepository;

    function __construct(InvitationRepository $invitationRepository)
    {
        $this->invitationRepository = $invitationRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param string $activityId
     * @param int $page
     * @param int $pageSize
     * @return Invitation[]
     */
    public function showAll(string $firmId, string $personnelId, string $activityId, int $page, int $pageSize)
    {
        return $this->invitationRepository->allInvitationsInConsultantActivity(
                        $firmId, $personnelId, $activityId, $page, $pageSize);
    }

    public function showById(string $firmId, string $personnelId, string $invitationId): Invitation
    {
        return $this->invitationRepository->anInvitationFromConsultant($firmId, $personnelId, $invitationId);
    }

}
