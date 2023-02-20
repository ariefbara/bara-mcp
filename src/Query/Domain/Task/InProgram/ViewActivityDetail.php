<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\ActivityType\ActivityRepository;

class ViewActivityDetail implements ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var ActivityRepository
     */
    protected $activityRepository;

    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->activityRepository->activityDetailInProgram($programId, $payload->getId());
    }

}
