<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Program\Activity;

class ViewActivity
{

    /**
     *
     * @var ActivityRepository
     */
    protected $activityRepository;

    function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function showById(string $firmId, string $activityId): Activity
    {
        return $this->activityRepository->anActivityInFirm($firmId, $activityId);
    }

}
