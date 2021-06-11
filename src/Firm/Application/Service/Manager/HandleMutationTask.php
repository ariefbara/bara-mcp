<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\MutationTaskExecutableByManager;

class HandleMutationTask
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

    public function execute(string $firmId, string $managerId, MutationTaskExecutableByManager $task): void
    {
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->handleMutationTask($task);
        $this->managerRepository->update();
    }

}
