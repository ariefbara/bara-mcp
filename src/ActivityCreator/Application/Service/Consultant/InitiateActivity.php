<?php

namespace ActivityCreator\Application\Service\Consultant;

use ActivityCreator\Application\Service\ActivityTypeRepository;
use Resources\Application\Event\Dispatcher;

class InitiateActivity
{

    /**
     *
     * @var ConsultantActivityRepository
     */
    protected $consultantActivityRepository;

    /**
     *
     * @var ConsultantRepository
     */
    protected $consultantRepository;

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
            ConsultantActivityRepository $consultantActivityRepository, ConsultantRepository $consultantRepository,
            ActivityTypeRepository $activityTypeRepository, Dispatcher $dispatcher)
    {
        $this->consultantActivityRepository = $consultantActivityRepository;
        $this->consultantRepository = $consultantRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $personnelId, string $consultantId, string $activityTypeId, $activityDataProvider): string
    {
        $id = $this->consultantActivityRepository->nextIdentity();
        $activityType = $this->activityTypeRepository->ofId($activityTypeId);
        $consultantActivity = $this->consultantRepository
                ->aConsultantBelongsToPersonnel($firmId, $personnelId, $consultantId)
                ->initiateActivity($id, $activityType, $activityDataProvider);
        $this->consultantActivityRepository->add($consultantActivity);

        $this->dispatcher->dispatch($consultantActivity);

        return $id;
    }

}
