<?php

namespace Query\Application\Service\Personnel;

use Query\Domain\Task\Personnel\PersonnelTask;

class ExecutePersonnelTask
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

    public function execute(string $firmId, string $personnelId, PersonnelTask $task, $payload): void
    {
        $this->personnelRepository->aPersonnelInFirm($firmId, $personnelId)
                ->executePersonnelTask($task, $payload);
    }

}
