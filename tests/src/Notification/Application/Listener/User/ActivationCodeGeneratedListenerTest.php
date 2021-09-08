<?php

namespace Notification\Application\Listener\User;

use Notification\Application\Service\ {
    SendImmediateMail,
    User\CreateActivationMail
};
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class ActivationCodeGeneratedListenerTest extends TestBase
{
    protected $service;
    protected $listener;
    protected $event, $userId = "userId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(CreateActivationMail::class);
        $this->listener = new ActivationCodeGeneratedListener($this->service);
        $this->event = $this->buildMockOfClass(CommonEvent::class);
        $this->event->expects($this->any())->method("getId")->willReturn($this->userId);
    }
    
    protected function executeHandle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeService()
    {
        $this->service->expects($this->once())
                ->method("execute")
                ->with($this->userId);
        $this->executeHandle();
    }
}
