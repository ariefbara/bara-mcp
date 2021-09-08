<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    GenerateNotificationWhenCommentSubmittedByConsultant,
    SendImmediateMail
};
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class CommentSubmittedByConsultantListenerTest extends TestBase
{
    protected $service;
    protected $listener;
    protected $event, $id = "commentId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(GenerateNotificationWhenCommentSubmittedByConsultant::class);
        $this->listener = new CommentSubmittedByConsultantListener($this->service);
        
        $this->event = $this->buildMockOfClass(CommonEvent::class);
        $this->event->expects($this->any())->method("getId")->willReturn($this->id);
    }
    
    protected function executeHandle()
    {
        $this->listener->handle($this->event);
    }
    
    public function test_handle_executeService()
    {
        $this->service->expects($this->once())
                ->method("execute")
                ->with($this->id);
        $this->executeHandle();
    }
}
