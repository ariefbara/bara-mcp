<?php

namespace Query\Application\Service\Consultant;

use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByConsultant;

class ExecuteTaskInProgram
{

    /**
     * 
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    public function __construct(ConsultantRepository $consultantRepository)
    {
        $this->consultantRepository = $consultantRepository;
    }

    public function execute(
            string $firmId, string $personnelId, string $consultantId, ITaskInProgramExecutableByConsultant $task): void
    {
        $this->consultantRepository->aConsultantBelongsToPersonnel($firmId, $personnelId, $consultantId)
                ->executeTaskInProgram($task);
    }

}
