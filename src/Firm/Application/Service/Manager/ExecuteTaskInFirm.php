<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\TaskInFirmExecutableByManager;

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
    
    public function execute(string $firmId, string $managerId, TaskInFirmExecutableByManager $firmTask, $payload): void
    {
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->executeTaskInFirm($firmTask, $payload);
        $this->managerRepository->update();
    }

}
