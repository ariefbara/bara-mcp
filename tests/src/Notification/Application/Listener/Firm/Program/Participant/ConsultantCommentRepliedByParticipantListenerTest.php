<?php

namespace Notification\Application\Listener\Firm\Program\Participant;

use Notification\Application\Service\ {
    GenerateConsultantCommentRepliedByParticipantNotification,
    SendImmediateMail
};
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class ConsultantCommentRepliedByParticipantListenerTest extends TestBase
{

    protected $generateConsultantCommentRepliedByParticipantNotification;
    protected $sendImmediateMail;
    protected $listener;
    protected $event, $id = "commentId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->generateConsultantCommentRepliedByParticipantNotification = $this->buildMockOfClass(
                GenerateConsultantCommentRepliedByParticipantNotification::class);
        $this->sendImmediateMail = $this->buildMockOfClass(SendImmediateMail::class);
        $this->listener = new ConsultantCommentRepliedByParticipantListener(
                $this->generateConsultantCommentRepliedByParticipantNotification, $this->sendImmediateMail);
        
        $this->event = $this->buildMockOfClass(CommonEvent::class);
        $this->event->expects($this->once())->method("getId")->willReturn($this->id);
    }
    
    protected function handle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeService()
    {
        $this->generateConsultantCommentRepliedByParticipantNotification->expects($this->once())
                ->method("execute")
                ->with($this->id);
        $this->handle();
    }
    public function test_handle_sendImmediateMail()
    {
        $this->sendImmediateMail->expects($this->once())
                ->method("execute");
        $this->handle();
    }

}
