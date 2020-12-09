<?php

namespace Notification\Domain\Model\Firm\Manager;

use Notification\Domain\Model\Firm\Manager;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotificationForAllUser;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ManagerMeetingAttendeeTest extends TestBase
{
    protected $managerMeetingAttendee;
    protected $manager;
    protected $mailGenerator, $mailMessage;
    protected $notification;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->managerMeetingAttendee = new TestableManagerMeetingAttendee();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerMeetingAttendee->manager = $this->manager;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->notification = $this->buildMockOfClass(ContainNotificationForAllUser::class);
    }
    
    public function test_registerAsMailRecipient_registerManagerAsMailRecipient()
    {
        $this->manager->expects($this->once())
                ->method("registerAsMailRecipient");
        $this->managerMeetingAttendee->registerAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    
    public function test_registerAsNotificationRecipient_addManagerAsNotificationRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addManagerRecipient")
                ->with($this->manager);
        $this->managerMeetingAttendee->registerAsNotificationRecipient($this->notification);
    }
}

class TestableManagerMeetingAttendee extends ManagerMeetingAttendee
{
    public $manager;
    public $id;
    public $meetingAttendee;
    
    function __construct()
    {
        parent::__construct();
    }
}
