<?php

namespace Bara\Application\Listener;

use Bara\Application\Service\SendUserActivationCodeMail;
use Tests\TestBase;

class SendMailWhenUserActivationCodeGeneratedListenerTest extends TestBase
{
    protected $listener;
    protected $service;
    protected $event, $userId = 'userId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(SendUserActivationCodeMail::class);
        $this->listener = new SendMailWhenUserActivationCodeGeneratedListener($this->service);
        
        $this->event = $this->buildMockOfInterface(UserActivationCodeGeneratedEventInterface::class);
        $this->event->expects($this->any())->method('getUserId')->willReturn($this->userId);
    }
    
    public function test_handle_executeService()
    {
        $this->service->expects($this->once())
                ->method('execute')
                ->with($this->userId);
        $this->listener->handle($this->event);
    }
}
