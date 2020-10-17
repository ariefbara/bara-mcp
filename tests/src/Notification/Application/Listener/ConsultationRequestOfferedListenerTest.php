<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    GenerateNotificationWhenConsultationRequestOffered,
    SendImmediateMail
};
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class ConsultationRequestOfferedListenerTest extends TestBase
{
    protected $service;
    protected $sendImmediateMail;
    protected $listener;
    protected $event, $id = "consultationRequestId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(GenerateNotificationWhenConsultationRequestOffered::class);
        $this->sendImmediateMail = $this->buildMockOfClass(SendImmediateMail::class);
        $this->listener = new ConsultationRequestOfferedListener($this->service, $this->sendImmediateMail);
        
        $this->event = $this->buildMockOfClass(CommonEvent::class);
        $this->event->expects($this->any())->method("getId")->willReturn($this->id);
    }
    
    protected function executeHandle()
    {
        $this->listener->handle($this->event);
    }
    public function test_execute_executeService()
    {
        $this->service->expects($this->once())
                ->method("execute")
                ->with($this->id);
        $this->executeHandle();
    }
    public function test_execute_sendMail()
    {
        $this->sendImmediateMail->expects($this->once())
                ->method("execute");
        $this->executeHandle();
    }
}
