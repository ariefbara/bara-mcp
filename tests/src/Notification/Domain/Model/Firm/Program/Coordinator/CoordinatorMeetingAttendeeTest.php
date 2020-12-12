<?php

namespace Notification\Domain\Model\Firm\Program\Coordinator;

use Notification\Domain\Model\Firm\Program\Coordinator;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotification;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class CoordinatorMeetingAttendeeTest extends TestBase
{
    protected $coordinatorMeetingAttendee;
    protected $coordinator;
    protected $mailGenerator, $mailMessage;
    protected $notification;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->coordinatorMeetingAttendee = new TestableCoordinatorMeetingAttendee();
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorMeetingAttendee->coordinator = $this->coordinator;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->notification = $this->buildMockOfClass(ContainNotification::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }
    
    public function test_registerAsMailRecipient_registerCoordinatorAsMailRecipient()
    {
        $this->coordinator->expects($this->once())
                ->method("registerAsMailRecipient");
        $this->coordinatorMeetingAttendee->registerAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    
    public function test_registerAsNotificationRecipient_registerCoordinatorAsNotificationRecipient()
    {
        $this->coordinator->expects($this->once())
                ->method("registerAsNotificationRecipient");
        $this->coordinatorMeetingAttendee->registerAsNotificationRecipient($this->notification);
    }
    
}

class TestableCoordinatorMeetingAttendee extends CoordinatorMeetingAttendee
{
    public $coordinator;
    public $id;
    public $meetingAttendee;
    
    function __construct()
    {
        parent::__construct();
    }
}
