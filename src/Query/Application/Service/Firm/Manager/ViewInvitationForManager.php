<?php

namespace Query\Application\Service\Firm\Manager;

use Query\Domain\Model\Firm\Manager\ManagerInvitation;

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

    public function showAll(string $firmId, string $managerId, int $page, int $pageSize)
    {
        return $this->managerInvitationRepository->allInvitationsForManager($firmId, $managerId, $page, $pageSize);
    }

    public function showById(string $firmId, string $managerId, string $invitationId): ManagerInvitation
    {
        return $this->managerInvitationRepository->anInvitationForManager($firmId, $managerId, $invitationId);
    }

}
