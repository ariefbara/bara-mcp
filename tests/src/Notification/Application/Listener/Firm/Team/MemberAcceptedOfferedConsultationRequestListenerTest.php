<?php

namespace Notification\Application\Listener\Firm\Team;

use Notification\Application\Service\ {
    AddConsultationSessionScheduledNotificationTriggeredByTeamMember,
    SendImmediateMail
};
use Tests\TestBase;

class MemberAcceptedOfferedConsultationRequestListenerTest extends TestBase
{

    protected $addConsultationSessionScheduledNotificationTriggeredByTeamMember;
    protected $listener;
    protected $event, $memberId = "memberId", $id = "id";

    protected function setUp(): void
    {
        parent::setUp();
        $this->addConsultationSessionScheduledNotificationTriggeredByTeamMember = 
                $this->buildMockOfClass(AddConsultationSessionScheduledNotificationTriggeredByTeamMember::class);
        $this->listener = new MemberAcceptedOfferedConsultationRequestListener(
                $this->addConsultationSessionScheduledNotificationTriggeredByTeamMember);
        
        $this->event = $this->buildMockOfClass(TriggeredByTeamMemberEventInterface::class);
        $this->event->expects($this->any())->method("getMemberId")->willReturn($this->memberId);
        $this->event->expects($this->any())->method("getId")->willReturn($this->id);
    }
    
    protected function executeHandle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeAddConsultationSessionNotification()
    {
        $this->addConsultationSessionScheduledNotificationTriggeredByTeamMember->expects($this->once())
                ->method("execute")
                ->with($this->memberId, $this->id);
        $this->executeHandle();
    }

}
