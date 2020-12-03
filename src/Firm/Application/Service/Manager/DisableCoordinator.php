<?php

namespace Firm\Application\Service\Manager;

class DisableCoordinator
{

    /**
     *
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(CoordinatorRepository $coordinatorRepository, ManagerRepository $managerRepository)
    {
        $this->coordinatorRepository = $coordinatorRepository;
        $this->managerRepository = $managerRepository;
    }
    
    public function execute(string $firmId, string $managerId, string $coordinatorId): void
    {
        $coordinator = $this->coordinatorRepository->aCoordinatorOfId($coordinatorId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->disableCoordinator($coordinator);
        $this->coordinatorRepository->update();
    }

}
