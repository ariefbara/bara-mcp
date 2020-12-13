<?php

namespace Query\Application\Service\Firm\Manager;

use Query\Domain\Model\Firm\Manager\ManagerNotificationRecipient;

class ViewNotification
{

    /**
     * 
     * @var ManagerNotificationRecipientRepository
     */
    protected $managerNotificationRecipientRepository;

    function __construct(ManagerNotificationRecipientRepository $managerNotificationRecipientRepository)
    {
        $this->managerNotificationRecipientRepository = $managerNotificationRecipientRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $managerId
     * @param int $page
     * @param int $pageSize
     * @return ManagerNotificationRecipient[]
     */
    public function showAll(string $firmId, string $managerId, int $page, int $pageSize)
    {
        return $this->managerNotificationRecipientRepository->allNotificationForManager($firmId, $managerId, $page,
                        $pageSize);
    }

    public function showById(string $firmId, string $managerId, string $managerNotificationId): ManagerNotificationRecipient
    {
        return $this->managerNotificationRecipientRepository->aNotificationForManager($firmId, $managerId,
                        $managerNotificationId);
    }

}
