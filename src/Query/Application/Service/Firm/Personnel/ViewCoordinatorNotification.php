<?php

namespace Query\Application\Service\Firm\Personnel;

use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorNotificationRecipient;

class ViewCoordinatorNotification
{

    /**
     * 
     * @var CoordinatorNotificationRepository
     */
    protected $coordinatorNotificationRepository;

    function __construct(CoordinatorNotificationRepository $coordinatorNotificationRepository)
    {
        $this->coordinatorNotificationRepository = $coordinatorNotificationRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param string $coordinatorId
     * @param int $page
     * @param int $pageSize
     * @return CoordinatorNotificationRecipient[]
     */
    public function showAll(string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize)
    {
        return $this->coordinatorNotificationRepository->allNotificationForCoordinator(
                        $firmId, $personnelId, $coordinatorId, $page, $pageSize);
    }

}
