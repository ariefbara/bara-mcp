<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use DateTimeImmutable;
use Personnel\ {
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Application\Service\Firm\Personnel\ProgramConsultantRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ConsultationRequestOfferTest extends TestBase
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
        
        $this->service = new ConsultationRequestOffer($this->programConsultantRepository, $this->dispatcher);
        
        $this->startTime = new DateTimeImmutable('+1 days');
    }
    
    protected function execute()
    {
        $this->service->execute(
            $this->personnelCompositionId, $this->programConsultantId, $this->consultationRequestId, 
            $this->startTime);
    }
    public function test_execute_offerNegotitateMentoringScheduleTimeInProgramConsultant()
    {
        $this->programConsultant->expects($this->once())
            ->method('offerConsultationRequestTime')
            ->with($this->consultationRequestId, $this->startTime);
        $this->execute();
    }
    public function test_execute_updateProgramConsultantRepository()
    {
        $this->programConsultantRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
    public function test_execute_dispatchDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->programConsultant);
        $this->execute();
    }
}
