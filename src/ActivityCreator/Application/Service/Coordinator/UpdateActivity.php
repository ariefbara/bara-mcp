<?php

namespace ActivityCreator\Application\Service\Coordinator;

use ActivityCreator\Domain\service\ActivityDataProvider;
use Resources\Application\Event\Dispatcher;

class UpdateActivity
{

    /**
     *
     * @var CoordinatorActivityRepository
     */
    protected $coordinatorActivityRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(CoordinatorActivityRepository $activityRepository, Dispatcher $dispatcher)
    {
        $this->coordinatorActivityRepository = $activityRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $personnelId, string $coordinatorActivityId, $activityDataProvider): void
    {
        $coordinatorActivity = $this->coordinatorActivityRepository
                ->aCoordinatorActivityBelongsToPersonnel($firmId, $personnelId, $coordinatorActivityId);
        $coordinatorActivity->update($activityDataProvider);

        $this->coordinatorActivityRepository->update();

        $this->dispatcher->dispatch($coordinatorActivity);
    }

}
