<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Participant\Domain\Task\Dependency\Firm\Program\MentorRepository;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ConsultationSessionRepository;

class DeclareConsultationSessionTask implements ITaskExecutableByParticipant
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
     * @var MentorRepository
     */
    protected $mentorRepository;

    /**
     * 
     * @var DeclareConsultationSessionPayload
     */
    protected $payload;

    /**
     * 
     * @var string|null
     */
    public $declaredSessionId;

    public function __construct(
            ConsultationSessionRepository $consultationSessionRepository,
            ConsultationSetupRepository $consultationSetupRepository, MentorRepository $mentorRepository,
            DeclareConsultationSessionPayload $payload)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->mentorRepository = $mentorRepository;
        $this->payload = $payload;
        $this->declaredSessionId = null;
    }

    public function execute(Participant $participant): void
    {
        $this->declaredSessionId = $this->consultationSessionRepository->nextIdentity();
        $consultationSetup = $this->consultationSetupRepository->ofId($this->payload->getConsultationSetupId());
        $consultant = $this->mentorRepository->ofId($this->payload->getMentorId());

        $consultationSession = $participant->declareConsultationSession(
                $this->declaredSessionId, $consultationSetup, $consultant, $this->payload->getStartEndTime(),
                $this->payload->getConsultationChannel());
        $this->consultationSessionRepository->add($consultationSession);
    }

}
