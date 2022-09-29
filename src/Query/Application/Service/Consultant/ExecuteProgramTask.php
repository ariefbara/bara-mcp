<?php

namespace Query\Application\Service\Consultant;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;

class ExecuteProgramTask
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
            string $firmId, string $personnelId, string $consultantId, ProgramTaskExecutableByConsultant $task, $payload): void
    {
        $this->consultantRepository->aConsultantBelongsToPersonnel($firmId, $personnelId, $consultantId)
                ->executeProgramTask($task, $payload);
    }

}
