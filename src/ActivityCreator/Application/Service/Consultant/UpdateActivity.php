<?php

namespace ActivityCreator\Application\Service\Consultant;

use Resources\Application\Event\Dispatcher;

class UpdateActivity
{
    /**
     *
     * @var ConsultantActivityRepository
     */
    protected $consultantActivityRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(ConsultantActivityRepository $activityRepository, Dispatcher $dispatcher)
    {
        $this->consultantActivityRepository = $activityRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $personnelId, string $consultantActivityId, $activityDataProvider): void
    {
        $consultantActivity = $this->consultantActivityRepository
                ->aConsultantActivityBelongsToPersonnel($firmId, $personnelId, $consultantActivityId);
        $consultantActivity->update($activityDataProvider);

        $this->consultantActivityRepository->update();

        $this->dispatcher->dispatch($consultantActivity);
    }
}
