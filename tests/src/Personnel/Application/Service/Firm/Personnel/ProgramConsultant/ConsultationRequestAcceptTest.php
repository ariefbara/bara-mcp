<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use DateTimeImmutable;
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultantRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ConsultationRequestAcceptTest extends TestBase
{
    protected $service;
    protected $personnelCompositionId;
    protected $programConsultantRepository, $programConsultant;
    protected $dispatcher;
    
    protected $firmId = 'firmId', $personnelId = 'personnelId', $programConsultationId = 'programConsultationId', 
            $consultationRequestId = 'consultationRequetId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programConsultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->programConsultantRepository = $this->buildMockOfInterface(ProgramConsultantRepository::class);
        $this->programConsultantRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->personnelId, $this->programConsultationId)
            ->willReturn($this->programConsultant);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new ConsultationRequestAccept($this->programConsultantRepository, $this->dispatcher);
        
        $this->startTime = new DateTimeImmutable('+1 days');
    }
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->programConsultationId, $this->consultationRequestId);
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
