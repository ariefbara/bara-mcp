<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionRepository,
    Application\Service\Firm\PersonnelRepository,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\Program\Participant\ConsultationSession
};
use Tests\TestBase;

class PersonnelNotificationOnConsultationSessionAddTest extends TestBase
{
    protected $service;
    protected $personnelNotificationOnConsultationSessionRepository;
    protected $personnelRepository, $personnel;
    protected $consultationSessionRepository, $consultationSession, $consultationSessionId = 'consultationSessionId';
    protected $message = 'new notification  message';


    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelNotificationOnConsultationSessionRepository = 
                $this->buildMockOfInterface(PersonnelNotificationOnConsultationSessionRepository::class);
        
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnelRepository->expects($this->any())
                ->method('aPersonnelHavingConsultationSession')
                ->with($this->consultationSessionId)
                ->willReturn($this->personnel);
        
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository->expects($this->any())
                ->method('aConsultationSessionById')
                ->with($this->consultationSessionId)
                ->willReturn($this->consultationSession);
        
        $this->service = new PersonnelNotificationOnConsultationSessionAdd(
                $this->personnelNotificationOnConsultationSessionRepository, $this->consultationSessionRepository, $this->personnelRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->consultationSessionId, $this->message);
    }
    
    public function test_execute_addPersonnelNotificationOnConsultationSessionToRepository()
    {
        $this->personnelNotificationOnConsultationSessionRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
}
