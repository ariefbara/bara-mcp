<?php

namespace Query\Application\Service\Firm\Manager\Activity;

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
     * @param string $managerId
     * @param string $activityId
     * @param int $page
     * @param int $pageSize
     * @return Invitation[]
     */
    public function showAll(string $firmId, string $managerId, string $activityId, int $page, int $pageSize)
    {
        return $this->invitationRepository
                        ->allInvitationInManagerActivity($firmId, $managerId, $activityId, $page, $pageSize);
    }

    public function showById(string $firmId, string $managerId, string $invitationId): Invitation
    {
        return $this->invitationRepository->anInvitationFromManager($firmId, $managerId, $invitationId);
    }

}
