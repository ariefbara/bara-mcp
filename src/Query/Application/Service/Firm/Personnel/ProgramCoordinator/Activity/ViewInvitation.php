<?php

namespace Query\Application\Service\Firm\Personnel\ProgramCoordinator\Activity;

use Query\Domain\Model\Firm\Program\Activity\Invitee;

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
     * @return Invitee[]
     */
    public function showAll(string $firmId, string $personnelId, string $activityId, int $page, int $pageSize)
    {
        return $this->invitationRepository->allInvitationsInCoordinatorActivity($firmId, $personnelId, $activityId,
                        $page, $pageSize);
    }

    public function showById(string $firmId, string $personnelId, string $invitationId): Invitee
    {
        return $this->invitationRepository->anInvitationFromCoordinator($firmId, $personnelId, $invitationId);
    }

}
