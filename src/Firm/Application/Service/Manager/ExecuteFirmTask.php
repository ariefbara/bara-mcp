<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\FirmTaskExecutableByManager;

class ExecuteFirmTask
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
    
    public function execute(string $firmId, string $managerId, FirmTaskExecutableByManager $task): void
    {
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->executeFirmTask($task);
        $this->managerRepository->update();
    }

}
