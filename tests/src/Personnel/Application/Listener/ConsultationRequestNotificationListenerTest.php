<?php

namespace Personnel\Application\Listener;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequest\PersonnelNotificationOnConsultationRequestAdd;
use Tests\TestBase;

class ConsultationRequestNotificationListenerTest extends TestBase
{
    protected $listener;
    protected $personnelNotificationOnConsultationRequestAdd;
    protected $event;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelNotificationOnConsultationRequestAdd = 
                $this->buildMockOfClass(PersonnelNotificationOnConsultationRequestAdd::class);
        $this->listener = new ConsultationRequestNotificationListener($this->personnelNotificationOnConsultationRequestAdd);
        
        $this->event = $this->buildMockOfInterface(EventInterfaceForPersonnelNotification::class);
    }
    
    public function test_hande_executeService()
    {
        $this->personnelNotificationOnConsultationRequestAdd->expects($this->once())
                ->method('execute');
        $this->listener->handle($this->event);
    }
}
