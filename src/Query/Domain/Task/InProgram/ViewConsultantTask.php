<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantTaskRepository;

class ViewConsultantTask implements ProgramTaskExecutableByConsultant, ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var ConsultantTaskRepository
     */
    protected $consultantTaskRepository;

    public function __construct(ConsultantTaskRepository $consultantTaskRepository)
    {
        $this->consultantTaskRepository = $consultantTaskRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->consultantTaskRepository->aConsultantTaskDetailInProgram($programId, $payload->getId());
    }

}
