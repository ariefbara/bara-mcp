<?php

namespace Query\Application\Service\User\ProgramParticipation\Activity;

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
     * @param string $userId
     * @param string $activityId
     * @param int $page
     * @param int $pageSize
     * @return Invitation[]
     */
    public function showAll(string $userId, string $activityId, int $page, int $pageSize)
    {
        return $this->invitationRepository
                        ->allInvitationsInUserParticipantActivity($userId, $activityId, $page, $pageSize);
    }

    public function showById(string $userId, string $invitationId): Invitation
    {
        return $this->invitationRepository->anInvitationFromUser($userId, $invitationId);
    }

}
