<?php

namespace ActivityCreator\Application\Service\Manager;

use Resources\Application\Event\Dispatcher;

class UpdateActivity
{

    /**
     *
     * @var ManagerActivityRepository
     */
    protected $managerActivityRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(ManagerActivityRepository $managerActivityRepository, Dispatcher $dispatcher)
    {
        $this->managerActivityRepository = $managerActivityRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $managerId, string $managerActivityId, $activityDataProvider): void
    {
        $managerActivity = $this->managerActivityRepository
                ->aManagerActivityOfId($firmId, $managerId, $managerActivityId);
        $managerActivity->update($activityDataProvider);
        $this->managerActivityRepository->update();
        $this->dispatcher->dispatch($managerActivity);
    }

}
