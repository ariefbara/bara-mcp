<?php

namespace ActivityCreator\Application\Service\Coordinator;

use ActivityCreator\Application\Service\ActivityTypeRepository;
use Resources\Application\Event\Dispatcher;

class InitiateActivity
{

    /**
     *
     * @var CoordinatorActivityRepository
     */
    protected $coordinatorActivityRepository;

    /**
     *
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    /**
     *
     * @var ActivityTypeRepository
     */
    protected $activityTypeRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            CoordinatorActivityRepository $coordinatorActivityRepository, CoordinatorRepository $coordinatorRepository,
            ActivityTypeRepository $activityTypeRepository, Dispatcher $dispatcher)
    {
        $this->coordinatorActivityRepository = $coordinatorActivityRepository;
        $this->coordinatorRepository = $coordinatorRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $personnelId, string $coordinatorId, string $activityTypeId, 
            $activityDataProvider): string
    {
        $id = $this->coordinatorActivityRepository->nextIdentity();
        $activityType = $this->activityTypeRepository->ofId($activityTypeId);
        $coordinatorActivity = $this->coordinatorRepository
                ->aCoordinatorBelongsToPersonnel($firmId, $personnelId, $coordinatorId)
                ->initiateActivity($id, $activityType, $activityDataProvider);
        $this->coordinatorActivityRepository->add($coordinatorActivity);
        
        $this->dispatcher->dispatch($coordinatorActivity);
        
        return $id;
    }

}
