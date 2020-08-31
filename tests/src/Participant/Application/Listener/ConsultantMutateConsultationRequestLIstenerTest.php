<?php

namespace Client\Application\Listener;

use Tests\TestBase;

class ConsultantMutateConsultationRequestLIstenerTest extends TestBase
{
    protected $clientNotificationRepository;
    protected $consultationRequestRepository;
    protected $listener;
    protected $event;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientNotificationRepository = $this->buildMockOfInterface(ClientNotificationRepository::class);
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->listener = new ConsultantMutateConsultationRequestListener(
                $this->clientNotificationRepository, $this->consultationRequestRepository);
        
        $this->event = $this->buildMockOfInterface(ConsultantMutateConsultationRequestEventInterface::class);
    }
    
    public function test_handle_addClientNotificationToRepository()
    {
        $this->clientNotificationRepository->expects($this->once())
                ->method('add');
        $this->listener->handle($this->event);
    }
}
