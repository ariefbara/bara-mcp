<?php

namespace Notification\Application\Listener\Client;

use Notification\Application\Service\ {
    Client\CreateClientResetPasswordMail,
    SendImmediateMail
};
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class ResetPasswordCodeGeneratedListenerTest extends TestBase
{
    protected $service;
    protected $listener;
    protected $event, $clientId = "clientId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(CreateClientResetPasswordMail::class);
        $this->listener = new ResetPasswordCodeGeneratedListener($this->service);
        $this->event = $this->buildMockOfClass(CommonEvent::class);
        $this->event->expects($this->any())->method("getId")->willReturn($this->clientId);
    }
    
    protected function executeHandle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeService()
    {
        $this->service->expects($this->once())
                ->method("execute")
                ->with($this->clientId);
        $this->executeHandle();
    }
}
