<?php

namespace Personnel\Application\Listener;
use Tests\TestBase;

class ParticipantMutateConsultationSessionListenerTest extends TestBase
{
    protected $personnelNotificationRepository;
    protected $consultationSessionRepository;
    protected $listener;
    protected $event;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelNotificationRepository = $this->buildMockOfInterface(PersonnelNotificationRepository::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->listener = new ParticipantMutateConsultationSessionListener(
                $this->personnelNotificationRepository, $this->consultationSessionRepository);
        
        $this->event = $this->buildMockOfInterface(ParticipantMutateConsultationSessionEventInterface::class);
    }
    
    public function test_handle_addNotificationToRepository()
    {
        $this->personnelNotificationRepository->expects($this->once())
                ->method('add');
        $this->listener->handle($this->event);
    }
}
