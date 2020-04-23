<?php
namespace Resources\Application\Listener;

use Tests\TestBase;

class SendMailListenerTest extends TestBase
{
    protected $listener;
    protected $sendMail;
    
    protected function setUp(): void {
        parent::setUp();
        $this->sendMail = $this->buildMockOfClass('\Resources\Application\Service\SendMail');
        $this->listener = new SendMailListener($this->sendMail);
    }
    
    function test_handle_executeMailSend() {
        $event = $this->buildMockOfInterface('Resources\Application\Listener\CanBeMailedEvent');
        $this->sendMail->expects($this->once())
            ->method('execute');
        $this->listener->handle($event);
    }
}

