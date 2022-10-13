<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequestData;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultationRequestRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;
use Resources\Application\Event\Dispatcher;
use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class ProposeConsultationTest extends MentorTaskTestBase
{

    protected $consultationRequestRepository, $consultationRequest, $consultationRequestId = 'consultationRequestId';
    protected $participantRepository, $participant, $participantId = 'participantId';
    protected $consultationSetupRepository, $consultationSetup, $consultationSetupId = 'consultationSetupId';
    protected $dispatcher;
    protected $task;
    protected $payload, $consultationRequestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        $this->task = new ProposeConsultation(
                $this->consultationRequestRepository, $this->participantRepository, $this->consultationSetupRepository,
                $this->dispatcher);

        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSetupRepository->expects($this->any())
                ->method('ofId')
                ->with($this->consultationSetupId)
                ->willReturn($this->consultationSetup);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantId)
                ->willReturn($this->participant);
        
        $this->consultationRequestData = $this->buildMockOfClass(ConsultationRequestData::class);
        $this->payload = new ProposeConsultationPayload(
                $this->participantId, $this->consultationSetupId, $this->consultationRequestData);
    }
    
    protected function execute()
    {
        $this->consultationRequestRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->consultationRequestId);
        
        $this->mentor->expects($this->any())
                ->method('proposeConsultation')
                ->with($this->consultationRequestId, $this->participant, $this->consultationSetup, $this->consultationRequestData)
                ->willReturn($this->consultationRequest);
        
        $this->task->execute($this->mentor, $this->payload);
    }
    public function test_execute_addConsultationRequestProposedByMentorToRepository()
    {
        $this->mentor->expects($this->once())
                ->method('proposeConsultation')
                ->with($this->consultationRequestId, $this->participant, $this->consultationSetup, $this->consultationRequestData)
                ->willReturn($this->consultationRequest);
        
        $this->consultationRequestRepository->expects($this->once())
                ->method('add')
                ->with($this->consultationRequest);
        $this->execute();
    }
    public function test_execute_setPayloadsProposedConsultationRequestId()
    {
        $this->execute();
        $this->assertSame($this->consultationRequestId, $this->payload->proposedConsultationRequestId);
    }
    public function test_execute_dispatchConsultationRequest()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->consultationRequest);
        $this->execute();
    }

}
