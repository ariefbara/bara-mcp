<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\ConsultationSetup;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByConsultant;
use Query\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;

class ViewAllConsultationSetupTask implements ITaskInProgramExecutableByConsultant
{

    /**
     * 
     * @var ConsultationSetupRepository
     */
    protected $consultationSetupRepository;

    /**
     * 
     * @var ViewAllConsultationSetupPayload
     */
    protected $payload;

    /**
     * 
     * @var ConsultationSetup[]|null
     */
    public $result;

    public function __construct(
            ConsultationSetupRepository $consultationSetupRepository, ViewAllConsultationSetupPayload $payload)
    {
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->payload = $payload;
        $this->result = null;
    }

    public function executeTaskInProgram(Program $program): void
    {
        $this->result = $this->consultationSetupRepository->allConsultationSetupsInProgram(
                $program->getId(), $this->payload->getPage(), $this->payload->getPageSize());
    }

}
