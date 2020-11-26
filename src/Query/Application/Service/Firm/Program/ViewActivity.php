<?php

namespace Query\Application\Service\Firm\Program;

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
    
    public function showById(string $firmId, string $programId, string $activityId): Activity
    {
        return $this->activityRepository->anActivityInProgram($firmId, $programId, $activityId);
    }

}
