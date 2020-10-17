<?php

namespace Query\Application\Service\Firm\Personnel;

use Query\Domain\Model\Firm\Personnel\PersonnelNotificationRecipient;

class ViewPersonnelNotification
{

    /**
     *
     * @var PersonnelNotificationRepository
     */
    protected $personnelNotificationRepository;

    public function __construct(PersonnelNotificationRepository $personnelNotificationRepository)
    {
        $this->personnelNotificationRepository = $personnelNotificationRepository;
    }

    /**
     * 
     * @param string $personnelId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $readStatus
     * @return PersonnelNotificationRecipient[]
     */
    public function showAll(string $personnelId, int $page, int $pageSize, ?bool $readStatus)
    {
        return $this->personnelNotificationRepository
                        ->allNotificationBelongsToPersonnel($personnelId, $page, $pageSize, $readStatus);
    }

}
