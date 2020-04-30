<?php

namespace Client\Application\Listener;

use Client\Application\Service\Client\ProgramParticipation\ConsultationRequest\ConsultationRequestNotificationAdd;
use Tests\TestBase;

class ConsultationRequestNotificationLIstenerTest extends TestBase
{
    protected $listener;
    protected $consultationRequestNotificationAdd;
    protected $event;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequestNotificationAdd = $this->buildMockOfClass(ConsultationRequestNotificationAdd::class);
        $this->listener = new ConsultationRequestNotificationLIstener($this->consultationRequestNotificationAdd);
        
        $this->event = $this->buildMockOfInterface(ConsultationRequestNotificationEventInterface::class);
    }
    
    public function test_handle_executeConsultationRequestNotificationAdd()
    {
        $this->consultationRequestNotificationAdd->expects($this->once())
                ->method('execute');
        $this->listener->handle($this->event);
    }
}
