<?php

namespace Client\Application\Listener;

use Client\Application\Service\Client\ProgramParticipation\ConsultationSession\ConsultationSessionNotificationAdd;
use Tests\TestBase;

class ConsultationSessionNotificationListenerTest extends TestBase
{
    protected $listener;
    protected $consultationSessionNotificationAdd;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSessionNotificationAdd = $this->buildMockOfClass(ConsultationSessionNotificationAdd::class);
        $this->listener = new ConsultationSessionNotificationListener($this->consultationSessionNotificationAdd);
        
        $this->event = $this->buildMockOfInterface(ConsultationSessionNotificationEventInterface::class);
    }
    
    public function test_handle_executeConsultationSessionNotificationAdd()
    {
        $this->consultationSessionNotificationAdd->expects($this->once())
                ->method('execute');
        $this->listener->handle($this->event);
    }
}
