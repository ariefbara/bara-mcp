<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultantRepository;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequestData;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ConsultationRequestOfferTest extends TestBase
{
    protected $service;
    protected $personnelCompositionId;
    protected $programConsultantRepository, $programConsultant;
    protected $dispatcher;
    
    protected $firmId = 'firmId', $personnelId = 'personnelId', $programConsultationId = 'programConsultationId', 
            $consultationRequestId = 'consultationRequetId';
    protected $consultationRequestData;
    
    protected function setUp(): void
    {
        parent::setUp();
        parent::setUp();
        $this->programConsultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->programConsultantRepository = $this->buildMockOfInterface(ProgramConsultantRepository::class);
        $this->programConsultantRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->personnelId, $this->programConsultationId)
            ->willReturn($this->programConsultant);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new ConsultationRequestOffer($this->programConsultantRepository, $this->dispatcher);
        
        $this->consultationRequestData = $this->buildMockOfClass(ConsultationRequestData::class);
    }
    
    protected function execute()
    {
        $this->service->execute(
            $this->firmId, $this->personnelId, $this->programConsultationId, $this->consultationRequestId, 
            $this->consultationRequestData);
    }
    public function test_execute_offerNegotitateMentoringScheduleTimeInProgramConsultant()
    {
        $this->programConsultant->expects($this->once())
            ->method('offerConsultationRequestTime')
            ->with($this->consultationRequestId, $this->consultationRequestData);
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
