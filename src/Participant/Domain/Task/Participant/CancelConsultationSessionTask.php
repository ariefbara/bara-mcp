<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ConsultationSessionRepository;

class CancelConsultationSessionTask implements ITaskExecutableByParticipant
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

    public function __construct(ConsultationSessionRepository $consultationSessionRepository,
            string $consultationSessionId)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
        $this->consultationSessionId = $consultationSessionId;
    }

    public function execute(Participant $participant): void
    {
        $consultationSession = $this->consultationSessionRepository->ofId($this->consultationSessionId);
        $consultationSession->assertManageableByParticipant($participant);
        $consultationSession->cancel();
    }

}
