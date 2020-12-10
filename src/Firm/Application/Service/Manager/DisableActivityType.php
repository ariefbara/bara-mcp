<?php

namespace Firm\Application\Service\Manager;

use Firm\Application\Service\Personnel\ActivityTypeRepository;

class DisableActivityType
{

    /**
     *
     * @var ActivityTypeRepository
     */
    protected $activityTypeRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(ActivityTypeRepository $activityTypeRepository, ManagerRepository $managerRepository)
    {
        $this->activityTypeRepository = $activityTypeRepository;
        $this->managerRepository = $managerRepository;
    }

    public function execute(string $firmId, string $managerId, string $activityTypeId): void
    {
        $activityType = $this->activityTypeRepository->ofId($activityTypeId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->disableActivityType($activityType);
        
        $this->activityTypeRepository->update();
    }

}
