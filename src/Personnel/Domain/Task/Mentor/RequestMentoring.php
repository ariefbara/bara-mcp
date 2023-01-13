<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringRequestRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class RequestMentoring implements MentorTask {

    /**
     * 
     * @var MentoringRequestRepository
     */
    protected $mentoringRequestRepository;

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

    public function __construct(
            MentoringRequestRepository $mentoringRequestRepository,
            ConsultationSetupRepository $consultationSetupRepository,
            ParticipantRepository $participantRepository) {
        $this->mentoringRequestRepository = $mentoringRequestRepository;
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->participantRepository = $participantRepository;
    }

    /**
     * 
     * @param ProgramConsultant $mentor
     * @param RequestMentoringPayload $payload
     * @return void
     */
    public function execute(ProgramConsultant $mentor, $payload): void {
        $payload->requestedMentoringId = $this->mentoringRequestRepository->nextIdentity();
        $consultationSetup = $this->consultationSetupRepository->ofId($payload->getConsultationSetupId());
        $participant = $this->participantRepository->ofId($payload->getParticipantId());
        $data = $payload->getMentoringRequestData();
        $mentoringRequest = $mentor->requestMentoring(
                $payload->requestedMentoringId, $consultationSetup, $participant, $data);
        $this->mentoringRequestRepository->add($mentoringRequest);
    }

}
