<?php

namespace Client\Application\Listener;

use Client\Application\Service\Client\ProgramParticipation\Worksheet\Comment\CommentNotificationFromConsultantAdd;
use Tests\TestBase;

class ConsultantCommentNotificationListenerTest extends TestBase
{
    protected $listener;
    protected $commentNotificationFromConsultantAdd;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commentNotificationFromConsultantAdd = $this->buildMockOfClass(CommentNotificationFromConsultantAdd::class);
        $this->listener = new ConsultantCommentNotificationListener($this->commentNotificationFromConsultantAdd);
        
        $this->event = $this->buildMockOfClass(ConsultantCommentNotificationEventInterface::class);
    }
    
    public function test_handle_executeCommentNotificationFromConsultantAdd()
    {
        $this->commentNotificationFromConsultantAdd->expects($this->once())
                ->method('execute');
        $this->listener->handle($this->event);
    }
}
