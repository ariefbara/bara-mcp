<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    CreateManagerResetPasswordMail,
    SendImmediateMail
};
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class ManagerResetPasswordCodeGeneratedListenerTest extends TestBase
{
    protected $createManagerResetPasswordMail;
    protected $listener;
    protected $event;
    protected $managerId = "managerId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->createManagerResetPasswordMail = $this->buildMockOfClass(CreateManagerResetPasswordMail::class);
        $this->listener = new ManagerResetPasswordCodeGeneratedListener($this->createManagerResetPasswordMail);
        
        $this->event = $this->buildMockOfClass(CommonEvent::class);
        $this->event->expects($this->any())->method("getId")->willReturn($this->managerId);
    }
    
    protected function executeHandle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeCreateManagerResetPasswordMail()
    {
        $this->createManagerResetPasswordMail->expects($this->once())
                ->method("execute")
                ->with($this->managerId);
        $this->executeHandle();
    }
}
