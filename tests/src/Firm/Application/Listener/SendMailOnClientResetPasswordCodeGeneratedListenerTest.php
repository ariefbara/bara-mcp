<?php

namespace Firm\Application\Listener;

use Firm\Application\Service\Firm\SendClientResetPasswordCodeMail;
use Tests\TestBase;

class SendMailOnClientResetPasswordCodeGeneratedListenerTest extends TestBase
{
    protected $listener;
    protected $service;
    protected $event, $firmId = 'firmId', $clientId = 'clientId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(SendClientResetPasswordCodeMail::class);
        $this->listener = new SendMailOnClientResetPasswordCodeGeneratedListener($this->service);
        
        $this->event = $this->buildMockOfInterface(ClientResetPasswordCodeGeneratedEventInterface::class);
        $this->event->expects($this->any())->method('getFirmId')->willReturn($this->firmId);
        $this->event->expects($this->any())->method('getClientId')->willReturn($this->clientId);
    }
    
    public function test_handle_executeService()
    {
        $this->service->expects($this->once())
                ->method('execute')
                ->with($this->firmId, $this->clientId);
        $this->listener->handle($this->event);
    }
}
