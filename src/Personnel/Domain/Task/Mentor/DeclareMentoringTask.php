<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\DeclaredMentoringRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class DeclareMentoringTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var DeclaredMentoringRepository
     */
    protected $declaredMentoringRepository;

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    /**
     * 
     * @var ConsultationSetupRepository
     */
    protected $consultationSetupRepository;

    /**
     * 
     * @var DeclareMentoringPayload
     */
    protected $payload;

    /**
     * 
     * @var string|null
     */
    public $declaredMentoringId;

    public function __construct(
            DeclaredMentoringRepository $declaredMentoringRepository, ParticipantRepository $participantRepository,
            ConsultationSetupRepository $consultationSetupRepository, DeclareMentoringPayload $payload)
    {
        $this->declaredMentoringRepository = $declaredMentoringRepository;
        $this->participantRepository = $participantRepository;
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->payload = $payload;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $this->declaredMentoringId = $this->declaredMentoringRepository->nextIdentity();
        $participant = $this->participantRepository->ofId($this->payload->getParticipantId());
        $consultationSetup = $this->consultationSetupRepository->ofId($this->payload->getConsultationSetupId());
        $declaredMentoring = $mentor->declareMentoring(
                $this->declaredMentoringId, $participant, $consultationSetup, $this->payload->getScheduleData());
        $this->declaredMentoringRepository->add($declaredMentoring);
    }

}
