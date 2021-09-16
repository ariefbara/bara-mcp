<?php

namespace Query\Application\Service\Firm\Manager;

use Query\Domain\Model\Firm\Manager\ManagerInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;


class ViewInvitationForManager
{

    /**
     *
     * @var ManagerInvitationRepository
     */
    protected $managerInvitationRepository;

    function __construct(ManagerInvitationRepository $managerInvitationRepository)
    {
        $this->managerInvitationRepository = $managerInvitationRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $managerId
     * @param int $page
     * @param int $pageSize
     * @return ManagerInvitee[]
     */
    public function showAll(string $firmId, string $managerId, int $page, int $pageSize, ?InviteeFilter $inviteeFilter)
    {
        return $this->managerInvitationRepository
                ->allInvitationsForManager($firmId, $managerId, $page, $pageSize, $inviteeFilter);
    }

    public function showById(string $firmId, string $managerId, string $invitationId): ManagerInvitee
    {
        return $this->managerInvitationRepository->anInvitationForManager($firmId, $managerId, $invitationId);
    }

}
