<?php

namespace Query\Application\Service\Firm\Personnel\ProgramCoordinator;

use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorActivity;

class ViewCoordinatorActivity
{

    /**
     *
     * @var CoordinatorActivityRepository
     */
    protected $coordinatorActivityRepository;

    function __construct(CoordinatorActivityRepository $coordinatorActivityRepository)
    {
        $this->coordinatorActivityRepository = $coordinatorActivityRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnellId
     * @param string $coordinatorId
     * @param int $page
     * @param int $pageSize
     * @return CoordinatorActivity[]
     */
    public function showAll(string $firmId, string $personnellId, string $coordinatorId, int $page, int $pageSize)
    {
        return $this->coordinatorActivityRepository->allActivitiesBelongsToCoordinator(
                        $firmId, $personnellId, $coordinatorId, $page, $pageSize);
    }

    public function showById(string $firmId, string $personnelId, string $activityId): CoordinatorActivity
    {
        return $this->coordinatorActivityRepository->anActivityBelongsToCoordinator($firmId, $personnelId, $activityId);
    }

}
