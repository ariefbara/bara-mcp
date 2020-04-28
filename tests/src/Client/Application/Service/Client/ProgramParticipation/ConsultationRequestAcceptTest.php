<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\ {
    Application\Service\Client\ProgramParticipationRepository,
    Domain\Model\Client\ProgramParticipation
};
use DateTimeImmutable;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ConsultationRequestAcceptTest extends TestBase
{
    protected $service;
    protected $clientId = "clientId";
    protected $programParticipationRepository, $programParticipation, $programParticipationId = 'programParticipation-id';
    protected $dispatcher;
    protected $consultationRequestId = 'negotiate-schedule-id';
    protected $startTime;

    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->programParticipationRepository = $this->buildMockOfInterface(ProgramParticipationRepository::class);
        $this->programParticipationRepository->expects($this->any())
            ->method('ofId')
            ->with($this->clientId, $this->programParticipationId)
            ->willReturn($this->programParticipation);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new ConsultationRequestAccept($this->programParticipationRepository, $this->dispatcher);
        
        $this->startTime = new DateTimeImmutable();
    }
    protected function execute()
    {
        $this->service->execute($this->clientId, $this->programParticipationId, $this->consultationRequestId);
    }
    public function test_execute_executeProgramParticipationsAcceptConsultationRequestMethod()
    {
        $this->programParticipation->expects($this->once())
            ->method('acceptConsultationRequest')
            ->with($this->consultationRequestId);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->programParticipationRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
    public function test_execute_dispatchDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->programParticipation);
        $this->execute();
    }
    
}
