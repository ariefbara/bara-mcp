<?php

namespace Query\Application\Service\Personnel;

use Query\Domain\Model\Firm\TaskExecutableByPersonnel;

class ExecuteQueryTask
{

    /**
     * 
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    public function __construct(PersonnelRepository $personnelRepository)
    {
        $this->personnelRepository = $personnelRepository;
    }
    
    public function execute(string $firmId, string $personnelId, TaskExecutableByPersonnel $task): void
    {
        $this->personnelRepository->aPersonnelInFirm($firmId, $personnelId)
                ->executeTask($task);
    }

}
