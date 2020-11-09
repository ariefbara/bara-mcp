<?php

namespace Query\Application\Service\Firm\Manager;

use Query\Domain\Model\Firm\Manager\ManagerActivity;

class ViewManagerActivity
{

    /**
     *
     * @var ManagerActivityRepository
     */
    protected $managerActivityRepository;

    function __construct(ManagerActivityRepository $managerActivityRepository)
    {
        $this->managerActivityRepository = $managerActivityRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $managerId
     * @param int $page
     * @param int $pageSize
     * @return ManagerActivity[]
     */
    public function showAll(string $firmId, string $managerId, int $page, int $pageSize)
    {
        return $this->managerActivityRepository->allActivitiesBelongsToManager($firmId, $managerId, $page, $pageSize);
    }

    public function showById(string $firmId, string $managerId, string $activityId): ManagerActivity
    {
        return $this->managerActivityRepository->anActivityBelongsToManager($firmId, $managerId, $activityId);
    }

}
