<?php

namespace Personnel\Application\Listener;

use Tests\TestBase;

class ParticipantMutateConsultationRequestListenerTest extends TestBase
{
    protected $personnelNotificationRepository;
    protected $consultationRequestRepository;
    protected $listener;
    protected $event;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelNotificationRepository = $this->buildMockOfInterface(PersonnelNotificationRepository::class);
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->listener = new ParticipantMutateConsultationRequestListener(
                $this->personnelNotificationRepository, $this->consultationRequestRepository);
        
        $this->event = $this->buildMockOfInterface(ParticipantMutateConsultationRequestEventInterface::class);
    }
    
    public function test_hande_addNotificationToRepository()
    {
        $this->personnelNotificationRepository->expects($this->once())
                ->method('add');
        $this->listener->handle($this->event);
    }
}
