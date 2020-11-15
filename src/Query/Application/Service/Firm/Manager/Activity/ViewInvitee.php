<?php

namespace Query\Application\Service\Firm\Manager\Activity;

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
     * @param string $managerId
     * @param string $activityId
     * @param int $page
     * @param int $pageSize
     * @return Invitee[]
     */
    public function showAll(string $firmId, string $managerId, string $activityId, int $page, int $pageSize)
    {
        return $this->inviteeRepository
                        ->allInviteesInManagerActivity($firmId, $managerId, $activityId, $page, $pageSize);
    }

    public function showById(string $firmId, string $managerId, string $inviteeId): Invitee
    {
        return $this->inviteeRepository->anInviteeInManagerActivity($firmId, $managerId, $inviteeId);
    }

}
