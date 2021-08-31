<?php

namespace Notification\Application\Listener\Firm\Team;

use Notification\Application\Service\GenerateConsultationRequestNotificationTriggeredByTeamMember;
use Notification\Application\Service\SendImmediateMail;
use SharedContext\Domain\ValueObject\MailMessageBuilder;
use Tests\TestBase;

class MemberSubmittedConsultationRequestListenerTest extends TestBase
{

    protected $generateConsultationRequestNotificationTriggeredByTeamMember;
    protected $listener;
    protected $event, $memberId = "memberId", $id = "id";

    protected function setUp(): void
    {
        parent::setUp();
        $this->generateConsultationRequestNotificationTriggeredByTeamMember = $this->buildMockOfClass(GenerateConsultationRequestNotificationTriggeredByTeamMember::class);
        $this->listener = new MemberSubmittedConsultationRequestListener(
                $this->generateConsultationRequestNotificationTriggeredByTeamMember);

        $this->event = $this->buildMockOfInterface(TriggeredByTeamMemberEventInterface::class);
        $this->event->expects($this->any())->method("getMemberId")->willReturn($this->memberId);
        $this->event->expects($this->any())->method("getId")->willReturn($this->id);
    }

    public function test_handle_executeService()
    {
        $this->generateConsultationRequestNotificationTriggeredByTeamMember->expects($this->once())
                ->method("execute")
                ->with($this->memberId, $this->id, MailMessageBuilder::CONSULTATION_REQUESTED);
        $this->listener->handle($this->event);
    }
}
