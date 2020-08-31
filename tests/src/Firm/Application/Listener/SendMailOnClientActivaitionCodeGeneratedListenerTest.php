<?php

namespace Firm\Application\Listener;

use Firm\Application\Service\Firm\SendClientActivationCodeMail;
use Tests\TestBase;

class SendMailOnClientActivaitionCodeGeneratedListenerTest extends TestBase
{
    protected $listener;
    
    protected $sendClientActivationCodeMail;
    protected $event;
    protected $firmId = 'firmId', $clientId = 'clientId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->sendClientActivationCodeMail = $this->buildMockOfClass(SendClientActivationCodeMail::class);
        
        $this->listener = new SendMailOnClientActivationCodeGeneratedListener($this->sendClientActivationCodeMail);
        
        $this->event = $this->buildMockOfInterface(ClientActivationCodeGeneratedEventInterface::class);
        $this->event->expects($this->any())->method('getFirmId')->willReturn($this->firmId);
        $this->event->expects($this->any())->method('getClientId')->willReturn($this->clientId);
    }
    
    public function test_handle_executeSendClientActivationCodeMail()
    {
        $this->sendClientActivationCodeMail->expects($this->once())
                ->method('execute')
                ->with($this->firmId, $this->clientId);
        $this->listener->handle($this->event);
    }
}
