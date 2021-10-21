<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultationSessionRepository;

class DenyDeclaredConsultationSessionTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    /**
     * 
     * @var string
     */
    protected $consultationSessionId;

    public function __construct(
            ConsultationSessionRepository $consultationSessionRepository, string $consultationSessionId)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
        $this->consultationSessionId = $consultationSessionId;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $consultationSession = $this->consultationSessionRepository->aConsultationSessionOfId($this->consultationSessionId);
        $consultationSession->assertManageableByMentor($mentor);
        $consultationSession->deny();
    }

}
