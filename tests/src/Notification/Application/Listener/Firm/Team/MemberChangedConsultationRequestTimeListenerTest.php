<?php

namespace Notification\Application\Listener\Firm\Team;

use Notification\Application\Service\GenerateConsultationRequestNotificationTriggeredByTeamMember;
use Notification\Application\Service\SendImmediateMail;
use SharedContext\Domain\ValueObject\MailMessageBuilder;
use Tests\TestBase;

class MemberChangedConsultationRequestTimeListenerTest extends TestBase
{
    protected $generateConsultationRequestNotificationTriggeredByTeamMember;
    protected $sendImmediateMail;
    protected $listener;
    protected $event, $memberId = "memberId", $id = "id";

    protected function setUp(): void
    {
        parent::setUp();
        $this->generateConsultationRequestNotificationTriggeredByTeamMember = $this->buildMockOfClass(GenerateConsultationRequestNotificationTriggeredByTeamMember::class);
        $this->sendImmediateMail = $this->buildMockOfClass(SendImmediateMail::class);
        $this->listener = new MemberChangedConsultationRequestTimeListener($this->generateConsultationRequestNotificationTriggeredByTeamMember, $this->sendImmediateMail);
        
        $this->event = $this->buildMockOfInterface(TriggeredByTeamMemberEventInterface::class);
        $this->event->expects($this->any())->method("getMemberId")->willReturn($this->memberId);
        $this->event->expects($this->any())->method("getId")->willReturn($this->id);
    }
    
    public function test_handle_executeeService()
    {
        $this->generateConsultationRequestNotificationTriggeredByTeamMember->expects($this->once())
                ->method("execute")
                ->with($this->memberId, $this->id, MailMessageBuilder::CONSULTATION_SCHEDULE_CHANGED);
        $this->listener->handle($this->event);
    }
    public function test_handle_sendImmediateMail()
    {
        $this->sendImmediateMail->expects($this->once())
                ->method("execute");
        $this->listener->handle($this->event);
    }
}
