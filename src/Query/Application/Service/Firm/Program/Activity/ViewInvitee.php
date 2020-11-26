<?php

namespace Query\Application\Service\Firm\Program\Activity;

use Query\Domain\Model\Firm\Program\Activity\Invitee;

class ViewInvitee
{

    /**
     *
     * @var InviteeRepository
     */
    protected $inviteeRepository;

    function __construct(InviteeRepository $inviteeRepository)
    {
        $this->inviteeRepository = $inviteeRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $meetingId
     * @param int $page
     * @param int $pageSize
     * @return Invitee[]
     */
    public function showAll(string $firmId, string $meetingId, int $page, int $pageSize, ?bool $initiatorStatus)
    {
        return $this->inviteeRepository->allInviteesInMeeting($firmId, $meetingId, $page, $pageSize, $initiatorStatus);
    }

    public function showById(string $firmId, string $meetingId, string $inviteeId): Invitee
    {
        return $this->inviteeRepository->anInviteeInMeeting($firmId, $meetingId, $inviteeId);
    }

}
