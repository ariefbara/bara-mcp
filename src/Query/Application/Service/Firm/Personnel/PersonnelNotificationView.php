<?php

namespace Query\Application\Service\Firm\Personnel;

use Query\Domain\Model\Firm\Personnel\PersonnelNotification;

class PersonnelNotificationView
{

    /**
     *
     * @var PersonnelNotificationRepository
     */
    protected $personnelNotificationRepository;

    function __construct(PersonnelNotificationRepository $personnelNotificationRepository)
    {
        $this->personnelNotificationRepository = $personnelNotificationRepository;
    }

    /**
     * 
     * @param PersonnelCompositionId $personnelCompositionId
     * @param int $page
     * @param int $pageSize
     * @return PersonnelNotification[]
     */
    public function showAll(PersonnelCompositionId $personnelCompositionId, int $page, int $pageSize)
    {
        return $this->personnelNotificationRepository->all($personnelCompositionId, $page, $pageSize);
    }

    public function showById(PersonnelCompositionId $personnelCompositionId, string $personnelNotificationId): PersonnelNotification
    {
        return $this->personnelNotificationRepository->ofId($personnelCompositionId, $personnelNotificationId);
    }

}
