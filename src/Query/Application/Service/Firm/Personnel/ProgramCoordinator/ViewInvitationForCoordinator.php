<?php

namespace Query\Application\Service\Firm\Personnel\ProgramCoordinator;

use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorInvitee;

class ViewInvitationForCoordinator
{

    /**
     *
     * @var CoordinatorInvitationRepository
     */
    protected $coordinatorInvitationRepository;

    function __construct(CoordinatorInvitationRepository $coordinatorInvitationRepository)
    {
        $this->coordinatorInvitationRepository = $coordinatorInvitationRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param string $coordinatorId
     * @param int $page
     * @param int $pageSize
     * @return CoordinatorInvitee[]
     */
    public function showAll(string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize)
    {
        return $this->coordinatorInvitationRepository->allInvitationsForCoordinator($firmId, $personnelId,
                        $coordinatorId, $page, $pageSize);
    }

    public function showById(string $firmId, string $personnelId, string $invitationId): CoordinatorInvitee
    {
        return $this->coordinatorInvitationRepository->anInvitationForCoordinator($firmId, $personnelId, $invitationId);
    }

}
