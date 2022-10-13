<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultationRequestRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;
use Resources\Application\Event\Dispatcher;

class ProposeConsultation implements MentorTask
{

    /**
     * 
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

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
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(
            ConsultationRequestRepository $consultationRequestRepository, ParticipantRepository $participantRepository,
            ConsultationSetupRepository $consultationSetupRepository, Dispatcher $dispatcher)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
        $this->participantRepository = $participantRepository;
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * 
     * @param ProgramConsultant $mentor
     * @param ProposeConsultationPayload $payload
     * @return void
     */
    public function execute(ProgramConsultant $mentor, $payload): void
    {
        $payload->proposedConsultationRequestId = $this->consultationRequestRepository->nextIdentity();
        $participant = $this->participantRepository->ofId($payload->getParticipantId());
        $consultationSetup = $this->consultationSetupRepository->ofId($payload->getConsultationSetupId());
        $consultationRequestData = $payload->getConsultationRequestData();
        
        $consultationRequest = $mentor->proposeConsultation(
                $payload->proposedConsultationRequestId, $participant, $consultationSetup, $consultationRequestData);
        
        $this->consultationRequestRepository->add($consultationRequest);
        
        $this->dispatcher->dispatch($consultationRequest);
    }

}
