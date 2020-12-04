<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\ActivityType;

class ViewActivityType
{

    /**
     *
     * @var ActivityTypeRepository
     */
    protected $activityTypeRepository;

    function __construct(ActivityTypeRepository $activityTypeRepository)
    {
        $this->activityTypeRepository = $activityTypeRepository;
    }

    public function showAll(string $programId, int $page, int $pageSize, ?bool $enabledOnly = true)
    {
        return $this->activityTypeRepository->allActivityTypesInProgram($programId, $page, $pageSize, $enabledOnly);
    }

    public function showById(string $programId, string $activityTypeId): ActivityType
    {
        return $this->activityTypeRepository->anActivityTypeInProgram($programId, $activityTypeId);
    }

}
