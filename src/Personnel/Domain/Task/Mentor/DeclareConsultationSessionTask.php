<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultationSessionRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\ConsultationChannel;

class DeclareConsultationSessionTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    /**
     * 
     * @var ConsultationSetupRepository
     */
    protected $consultationSetupRepository;

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    /**
     * 
     * @var DeclareConsultationSessionPayload
     */
    protected $payload;

    /**
     * 
     * @var string|null
     */
    public $id;

    public function __construct(
            ConsultationSessionRepository $consultationSessionRepository,
            ConsultationSetupRepository $consultationSetupRepository, ParticipantRepository $participantRepository,
            DeclareConsultationSessionPayload $payload)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->participantRepository = $participantRepository;
        $this->payload = $payload;
        $this->id = null;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $this->id = $this->consultationSessionRepository->nextIdentity();
        $participant = $this->participantRepository->ofId($this->payload->getParticipantId());
        $consultationSetup = $this->consultationSetupRepository->ofId($this->payload->getConsultationSetupeId());
        $startEndTime = new DateTimeInterval($this->payload->getStartTime(), $this->payload->getEndTime());
        $channel = new ConsultationChannel($this->payload->getMedia(), $this->payload->getAddress());
        
        $consultationSession = $mentor->declareConsultationSession(
                $this->id, $participant, $consultationSetup, $startEndTime, $channel);
        
        $this->consultationSessionRepository->add($consultationSession);
    }

}
