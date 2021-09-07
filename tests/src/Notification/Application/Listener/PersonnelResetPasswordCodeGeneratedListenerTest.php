<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    CreatePersonnelResetPasswordMail,
    SendImmediateMail
};
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class PersonnelResetPasswordCodeGeneratedListenerTest extends TestBase
{
    protected $createPersonnelResetPasswordMail;
    protected $listener;
    protected $event, $personnelId = "personnelId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->createPersonnelResetPasswordMail = $this->buildMockOfClass(CreatePersonnelResetPasswordMail::class);
        
        $this->listener = new PersonnelResetPasswordCodeGeneratedListener(
                $this->createPersonnelResetPasswordMail);
        
        $this->event = $this->buildMockOfClass(CommonEvent::class);
        $this->event->expects($this->once())->method("getId")->willReturn($this->personnelId);
    }
    
    protected function executeHandle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeService()
    {
        $this->createPersonnelResetPasswordMail->expects($this->once())
                ->method("execute")
                ->with($this->personnelId);
        $this->executeHandle();
    }
}
