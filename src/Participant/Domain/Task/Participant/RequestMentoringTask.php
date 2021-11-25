<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Participant\Domain\Task\Dependency\Firm\Program\MentorRepository;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestRepository;
use SharedContext\Domain\ValueObject\ScheduleData;

class RequestMentoringTask implements ITaskExecutableByParticipant
{

    /**
     * 
     * @var MentoringRequestRepository
     */
    protected $mentoringRequestRepository;

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
     * @var RequestMentoringPayload
     */
    protected $payload;

    /**
     * 
     * @var string|null
     */
    public $requestedMentoringId;

    public function __construct(
            MentoringRequestRepository $mentoringRequestRepository, MentorRepository $mentorRepository,
            ConsultationSetupRepository $consultationSetupRepository, RequestMentoringPayload $payload)
    {
        $this->mentoringRequestRepository = $mentoringRequestRepository;
        $this->mentorRepository = $mentorRepository;
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->payload = $payload;
    }

    public function execute(Participant $participant): void
    {
        $this->requestedMentoringId = $this->mentoringRequestRepository->nextIdentity();
        $mentor = $this->mentorRepository->ofId($this->payload->getMentorId());
        $consultationSetup = $this->consultationSetupRepository->ofId($this->payload->getConsultationSetupId());
        $mentoringRequest = $participant->requestMentoring(
                $this->requestedMentoringId, $mentor, $consultationSetup, $this->payload->getMentoringRequestData());
        $this->mentoringRequestRepository->add($mentoringRequest);
    }

}
