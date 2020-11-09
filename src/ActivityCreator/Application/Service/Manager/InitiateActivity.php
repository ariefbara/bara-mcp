<?php

namespace ActivityCreator\Application\Service\Manager;

use ActivityCreator\Application\Service\ActivityTypeRepository;
use Resources\Application\Event\Dispatcher;

class InitiateActivity
{

    /**
     *
     * @var ManagerActivityRepository
     */
    protected $managerActivityRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;

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
            ManagerActivityRepository $managerActivityRepository, ManagerRepository $managerRepository,
            ProgramRepository $programRepository, ActivityTypeRepository $activityTypeRepository, Dispatcher $dispatcher)
    {
        $this->managerActivityRepository = $managerActivityRepository;
        $this->managerRepository = $managerRepository;
        $this->programRepository = $programRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $managerId, string $programId, string $activityTypeId,
            $activityDataProvider): string
    {
        $id = $this->managerActivityRepository->nextIdentity();
        $program = $this->programRepository->ofId($programId);
        $activityType = $this->activityTypeRepository->ofId($activityTypeId);
        
        $managerActivity = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->initiateActivityInProgram($id, $program, $activityType, $activityDataProvider);
        $this->managerActivityRepository->add($managerActivity);
        $this->dispatcher->dispatch($managerActivity);
        return $id;
    }

}
