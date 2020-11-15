<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation\Activity;

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
     * @param string $clientId
     * @param string $activityId
     * @param int $page
     * @param int $pageSize
     * @return Invitee[]
     */
    public function showAll(string $firmId, string $clientId, string $activityId, int $page, int $pageSize)
    {
        return $this->invitationRepository->allInvitationsInClientParticipantActivity(
                        $firmId, $clientId, $activityId, $page, $pageSize);
    }

    public function showById(string $firmId, string $clientId, string $invitationId): Invitee
    {
        return $this->invitationRepository->anInvitationFromClient($firmId, $clientId, $invitationId);
    }

}
