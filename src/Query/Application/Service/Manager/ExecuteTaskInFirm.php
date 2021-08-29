<?php

namespace Query\Application\Service\Manager;

use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;

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
    
    public function execute(string $firmId, string $managerId, ITaskInFirmExecutableByManager $task): void
    {
        $this->managerRepository
                ->aManagerInFirm($firmId, $managerId)
                ->executeTaskInFirm($task);
    }

}
