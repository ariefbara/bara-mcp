<?php

namespace Firm\Application\Service\Manager;

class ExecuteTaskInFirm
{

    /**
     * 
     * @var ManagerRepository
     */
    protected $managerRepository;

    public function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    public function execute(string $firmId, string $managerId, $task, $payload): void
    {
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->executeTaskInFirm($task, $payload);
        $this->managerRepository->update();
    }

}
