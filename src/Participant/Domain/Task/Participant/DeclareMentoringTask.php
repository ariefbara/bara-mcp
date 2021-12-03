<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Participant\Domain\Task\Dependency\Firm\Program\MentorRepository;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\DeclaredMentoringRepository;

class DeclareMentoringTask implements ITaskExecutableByParticipant
{

    /**
     * 
     * @var DeclaredMentoringRepository
     */
    protected $declaredMentoringRepository;

    /**
     * 
     * @var MentorRepository
     */
    protected $mentorRepository;

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
            DeclaredMentoringRepository $declaredMentoringRepository, MentorRepository $mentorRepository,
            ConsultationSetupRepository $consultationSetupRepository, DeclareMentoringPayload $payload)
    {
        $this->declaredMentoringRepository = $declaredMentoringRepository;
        $this->mentorRepository = $mentorRepository;
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->payload = $payload;
    }

    public function execute(\Participant\Domain\Model\Participant $participant): void
    {
        $this->declaredMentoringId = $this->declaredMentoringRepository->nextIdentity();
        $mentor = $this->mentorRepository->ofId($this->payload->getMentorId());
        $consultationSetup = $this->consultationSetupRepository->ofId($this->payload->getConsultationSetupId());
        $declaredMentoring = $participant->declareMentoring(
                $this->declaredMentoringId, $mentor, $consultationSetup, $this->payload->getScheduleData());
        $this->declaredMentoringRepository->add($declaredMentoring);
    }

}
