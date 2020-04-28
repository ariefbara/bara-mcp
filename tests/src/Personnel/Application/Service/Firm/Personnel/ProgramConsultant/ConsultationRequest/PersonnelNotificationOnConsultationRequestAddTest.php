<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequest;

use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestRepository,
    Application\Service\Firm\PersonnelRepository,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest
};
use Tests\TestBase;

class PersonnelNotificationOnConsultationRequestAddTest extends TestBase
{
    protected $service;
    protected $personnelNotificationOnConsultationRequestRepository;
    protected $personnelRepository, $personnel;
    protected $consultationRequestRepository, $consultationRequest, $consultationRequestId = 'consultationRequestId';
    protected $message = 'new notification  message';


    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelNotificationOnConsultationRequestRepository = 
                $this->buildMockOfInterface(PersonnelNotificationOnConsultationRequestRepository::class);
        
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnelRepository->expects($this->any())
                ->method('aPersonnelHavingConsultationRequest')
                ->with($this->consultationRequestId)
                ->willReturn($this->personnel);
        
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequestRepository->expects($this->any())
                ->method('aConsultationRequestById')
                ->with($this->consultationRequestId)
                ->willReturn($this->consultationRequest);
        
        $this->service = new PersonnelNotificationOnConsultationRequestAdd($this->personnelNotificationOnConsultationRequestRepository, $this->consultationRequestRepository, $this->personnelRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->consultationRequestId, $this->message);
    }
    
    public function test_execute_addPersonnelNotificationOnConsultationRequestToRepository()
    {
        $this->personnelNotificationOnConsultationRequestRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
}
