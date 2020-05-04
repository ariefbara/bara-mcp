<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use DateTimeImmutable;
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultantRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant
};
use Query\Application\Service\Firm\Personnel\PersonnelCompositionId;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ConsultationRequestAcceptTest extends TestBase
{
    protected $service;
    protected $personnelCompositionId;
    protected $programConsultantRepository, $programConsultant, $programConsultantId = 'program-mentorship-id';
    protected $dispatcher;
    protected $consultationRequestId = 'negotiate-mentoring-schedule-id', $startTime;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelCompositionId = $this->buildMockOfClass(PersonnelCompositionId::class);
        
        $this->programConsultantRepository = $this->buildMockOfInterface(ProgramConsultantRepository::class);
        $this->programConsultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->programConsultantRepository->expects($this->any())
            ->method('ofId')
            ->with($this->personnelCompositionId, $this->programConsultantId)
            ->willReturn($this->programConsultant);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new ConsultationRequestAccept($this->programConsultantRepository, $this->dispatcher);
        
        $this->startTime = new DateTimeImmutable('+1 days');
    }
    protected function execute()
    {
        $this->service->execute(
            $this->personnelCompositionId, $this->programConsultantId, $this->consultationRequestId);
    }
    
    public function test_accept_acceptConsultationRequestOfProgramConsultant()
    {
        $this->programConsultant->expects($this->once())
            ->method('acceptConsultationRequest')
            ->with($this->consultationRequestId);
        $this->execute();
    }
    public function test_accept_updateProgramConsultantRepository()
    {
        $this->programConsultantRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
    public function test_accept_dispatcheDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->programConsultant);
        $this->execute();
    }
}
