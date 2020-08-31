<?php

namespace Bara\Application\Listener;

use Bara\Application\Service\SendUserResetPasswordCodeMail;
use Tests\TestBase;

class SendMailWhenUserResetPasswordCodeGeneratedListenerTest extends TestBase
{
    protected $listener;
    protected $service;
    protected $event, $userId = 'userId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(SendUserResetPasswordCodeMail::class);
        $this->listener = new SendMailWhenUserResetPasswordCodeGeneratedListener($this->service);
        
        $this->event = $this->buildMockOfInterface(UserResetPasswordCodeGeneratedEventInterface::class);
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
