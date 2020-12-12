<?php

namespace Notification\Domain\Model\Firm\Program\Consultant;

use Notification\Domain\Model\Firm\Program\Consultant;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotification;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ConsultantMeetingAttendeeTest extends TestBase
{
    protected $consultantMeetingAttendee;
    protected $consultant;
    protected $mailGenerator, $mailMessage;
    protected $notification;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantMeetingAttendee = new TestableConsultantMeetingAttendee();
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantMeetingAttendee->consultant = $this->consultant;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->notification = $this->buildMockOfClass(ContainNotification::class);
    }
    
    public function test_registerAsMailRecipient_registerConsultantAsMailRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerMailRecipient");
        $this->consultantMeetingAttendee->registerAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    
    public function test_registerAsNotificationRecipient_registerConsultantAsNotificationRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerNotificationRecipient");
        $this->consultantMeetingAttendee->registerAsNotificationRecipient($this->notification);
    }
}

class TestableConsultantMeetingAttendee extends ConsultantMeetingAttendee
{
    public $consultant;
    public $id;
    public $meetingAttendee;
    
    function __construct()
    {
        parent::__construct();
    }
}
