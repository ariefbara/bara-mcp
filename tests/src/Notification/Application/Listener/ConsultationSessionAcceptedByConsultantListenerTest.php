<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    GenerateNotificationWhenConsultationSessionAcceptedByConsultant,
    SendImmediateMail
};
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class ConsultationSessionAcceptedByConsultantListenerTest extends TestBase
{
    protected $service;
    protected $sendImmediateMail;
    protected $listener;
    protected $event, $id = "consultationSessionId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(GenerateNotificationWhenConsultationSessionAcceptedByConsultant::class);
        $this->sendImmediateMail = $this->buildMockOfClass(SendImmediateMail::class);
        $this->listener = new ConsultationSessionAcceptedByConsultantListener($this->service, $this->sendImmediateMail);
        
        $this->event = $this->buildMockOfClass(CommonEvent::class);
        $this->event->expects($this->any())->method("getId")->willReturn($this->id);
    }
    
    protected function executeHandle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeService()
    {
        $this->service->expects($this->once())
                ->method("execute")
                ->with($this->id);
        $this->executeHandle();
    }
    public function test_handle_sendMail()
    {
        $this->sendImmediateMail->expects($this->once())
                ->method("execute");
        $this->executeHandle();
    }
}
