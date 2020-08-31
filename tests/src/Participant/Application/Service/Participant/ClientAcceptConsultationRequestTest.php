<?php

namespace Participant\Application\Service\Participant;

use DateTimeImmutable;
use Participant\ {
    Application\Service\ClientParticipantRepository,
    Domain\Model\ClientParticipant
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ClientAcceptConsultationRequestTest extends TestBase
{
    protected $service;
    protected $clientParticipantRepository, $clientParticipant, $clientParticipantId = 'clientParticipant-id';
    protected $dispatcher;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $consultationRequestId = 'negotiate-schedule-id';
    protected $startTime;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->clientId, $this->programId)
            ->willReturn($this->clientParticipant);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new ClientAcceptConsultationRequest($this->clientParticipantRepository, $this->dispatcher);
        
        $this->startTime = new DateTimeImmutable();
    }
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->consultationRequestId);
    }
    public function test_execute_executeClientParticipantsAcceptConsultationRequestMethod()
    {
        $this->clientParticipant->expects($this->once())
            ->method('acceptConsultationRequest')
            ->with($this->consultationRequestId);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
    public function test_execute_dispatchDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->clientParticipant);
        $this->execute();
    }
    
}
