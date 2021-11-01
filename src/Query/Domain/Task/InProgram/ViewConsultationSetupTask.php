<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\ConsultationSetup;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByConsultant;
use Query\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;

class ViewConsultationSetupTask implements ITaskInProgramExecutableByConsultant
{

    /**
     * 
     * @var ConsultationSetupRepository
     */
    protected $consultationSetupRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var ConsultationSetup|null
     */
    public $result;

    public function __construct(ConsultationSetupRepository $consultationSetupRepository, string $id)
    {
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->id = $id;
        $this->result = null;
    }

    public function executeTaskInProgram(Program $program): void
    {
        $this->result = $this->consultationSetupRepository->aConsultationSetupInProgram($program->getId(), $this->id);
    }

}
