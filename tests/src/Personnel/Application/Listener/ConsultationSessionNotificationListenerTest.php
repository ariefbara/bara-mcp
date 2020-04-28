<?php

namespace Personnel\Application\Listener;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSession\PersonnelNotificationOnConsultationSessionAdd;
use Tests\TestBase;

class ConsultationSessionNotificationListenerTest extends TestBase
{
    protected $listener;
    protected $personnelNotificationOnConsultationSessionAdd;
    protected $event;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelNotificationOnConsultationSessionAdd = 
                $this->buildMockOfClass(PersonnelNotificationOnConsultationSessionAdd::class);
        $this->listener =  new ConsultationSessionNotificationListener($this->personnelNotificationOnConsultationSessionAdd);
        
        $this->event = $this->buildMockOfInterface(EventInterfaceForPersonnelNotification::class);
    }
    
    public function test_handle_executeService()
    {
        $this->personnelNotificationOnConsultationSessionAdd->expects($this->once())
                ->method('execute');
        $this->listener->handle($this->event);
    }
}
