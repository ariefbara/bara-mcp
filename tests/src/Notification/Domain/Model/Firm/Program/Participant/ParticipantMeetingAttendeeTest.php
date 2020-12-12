<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Notification\Domain\Model\Firm\Program\Participant;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotification;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ParticipantMeetingAttendeeTest extends TestBase
{
    protected $participantMeetingAttendee;
    protected $participant;
    protected $mailGenerator, $mailMessage;
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantMeetingAttendee = new TestableParticipantMeetingAttendee();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantMeetingAttendee->participant = $this->participant;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->notification = $this->buildMockOfInterface(ContainNotification::class);
    }
    
    public function test_registerAsMailRecipient_registerParticipantAsMailRecipient()
    {
        $this->participant->expects($this->once())
                ->method("registerMailRecipient");
        $this->participantMeetingAttendee->registerAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    
    public function test_registerAsNotificationRecipient_registerParticipantAsNotificationRecipient()
    {
        $this->participant->expects($this->once())
                ->method("registerNotificationRecipient");
        $this->participantMeetingAttendee->registerAsNotificationRecipient($this->notification);
    }
}

class TestableParticipantMeetingAttendee extends ParticipantMeetingAttendee
{
    public $participant;
    public $id;
    public $meetingAttendee;
    
    function __construct()
    {
        parent::__construct();
    }
}
