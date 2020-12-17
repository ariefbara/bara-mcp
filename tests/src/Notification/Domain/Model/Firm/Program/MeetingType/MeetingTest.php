<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm\Program\MeetingType;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingMail;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingNotification;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class MeetingTest extends TestBase
{
    protected $meeting;
    protected $meetingType;
    protected $startEndTime;
    protected $attendee;
    protected $mailMessage, $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";

    protected function setUp(): void
    {
        parent::setUp();
        $this->meeting = new TestableMeeting();
        $this->meeting->attendees = new ArrayCollection();
        $this->meeting->mails = new ArrayCollection();
        $this->meeting->notifications = new ArrayCollection();
        
        $this->meetingType = $this->buildMockOfClass(MeetingType::class);
        $this->meeting->meetingType = $this->meetingType;
        
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->meeting->startEndTime = $this->startEndTime;
        
        $this->attendee = $this->buildMockOfClass(MeetingAttendee::class);
        $this->meeting->attendees->add($this->attendee);
        
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }
    
    protected function executeAddMeetingCreatedNotification()
    {
        $this->attendee->expects($this->any())->method("isAnInitiator")->willReturn(true);
        $this->attendee->expects($this->any())->method("isCancelled")->willReturn(false);
        $this->meeting->addMeetingCreatedNotification();
    }
    public function test_addMeetingCreatedNotification_registerInitiatorAsMailRecipient()
    {
        $this->attendee->expects($this->once())
                ->method("registerAsMeetingMailRecipient")
                ->with($this->meeting, $this->anything());
        $this->executeAddMeetingCreatedNotification();
    }
    public function test_addMeetingCreatedNotification_registerInitiatorAsMeetingNotificationRecipient()
    {
        $this->attendee->expects($this->once())
                ->method("registerAsMeetingNotificationRecipient");
        $this->executeAddMeetingCreatedNotification();
    }
    public function test_addMeetingCreatedNotification_addMeetingNotificationToCollection()
    {
        $this->executeAddMeetingCreatedNotification();
        $this->assertEquals(1, $this->meeting->notifications->count());
        $this->assertInstanceOf(MeetingNotification::class, $this->meeting->notifications->first());
    }
    public function test_addMeetingCreatedNotification_noInitiatorInAttendeeList_nop()
    {
        $this->attendee->expects($this->once())->method("isAnInitiator")->willReturn(false);
        $this->executeAddMeetingCreatedNotification();
        $this->markAsSuccess();
    }
    
    protected function executeAddMeetingScheduleChangedNotification()
    {
        $this->attendee->expects($this->any())->method("isCancelled")->willReturn(false);
        $this->meeting->addMeetingScheduleChangedNotification();
    }
    public function test_addMeetingScheduleChangedNotification_registerAllAttendeeAsMailRecipient()
    {
        $this->attendee->expects($this->once())
                ->method("registerAsMeetingMailRecipient")
                ->with($this->meeting, $this->anything());
        $this->executeAddMeetingScheduleChangedNotification();
    }
    public function test_addMeetingScheduleChangedNotification_registerAllAttendeeAsMeetingNotificationRecipient()
    {
        $this->attendee->expects($this->once())
                ->method("registerAsMeetingNotificationRecipient");
        $this->executeAddMeetingScheduleChangedNotification();
    }
    public function test_addMeetingScheduleChangedNotification_addMeetingNotificationToCollection()
    {
        $this->executeAddMeetingScheduleChangedNotification();
        $this->assertEquals(1, $this->meeting->notifications->count());
        $this->assertInstanceOf(MeetingNotification::class, $this->meeting->notifications->first());
    }
    public function test_addMeetingScheduleChangedNotification_cancelledAttendee_preventRegisteringAsNotificaitonRecipient()
    {
        $this->attendee->expects($this->once())->method("isCancelled")->willReturn(true);
        $this->attendee->expects($this->never())
                ->method("registerAsMeetingMailRecipient");
        $this->attendee->expects($this->never())
                ->method("registerAsMeetingNotificationRecipient");
        $this->executeAddMeetingScheduleChangedNotification();
    }
    
    public function test_addMail_addMeetingMailNotCollection()
    {
        $this->meeting->addMail($this->mailMessage, $this->recipientMailAddress, $this->recipientName);
        $this->assertEquals(1, $this->meeting->mails->count());
        $this->assertInstanceOf(MeetingMail::class, $this->meeting->mails->first());
    }
    
    public function test_getScheduleInIndonesianFormat_returnStartEndDateGetTimeDescriptionInIndonesianFormatResult()
    {
        $this->startEndTime->expects($this->once())
                ->method("getTimeDescriptionInIndonesianFormat")
                ->willReturn("time description");
        $this->meeting->getscheduleInIndonesianFormat();
    }
    
    public function test_getFirmDomain_returnMeetingTypeGetFirmDomainResult()
    {
        $this->meetingType->expects($this->once())->method("getFirmDomain");
        $this->meeting->getFirmDomain();
    }
    
    public function test_getFirmLogoPath_returnMeetingTypeGetFirmLogoPathResult()
    {
        $this->meetingType->expects($this->once())->method("getFirmLogoPath");
        $this->meeting->getFirmLogoPath();
    }
    
    public function test_getFirmMailSenderAddress_returnMeetingTypeGetFirmMailSenderAddressResult()
    {
        $this->meetingType->expects($this->once())->method("getFirmMailSenderAddress");
        $this->meeting->getFirmMailSenderAddress();
    }
    
    public function test_getFirmMailSenderName_returnMeetingTypeGetFirmMailSenderNameResult()
    {
        $this->meetingType->expects($this->once())->method("getFirmMailSenderName");
        $this->meeting->getFirmMailSenderName();
    }
    
    public function test_getMeetingTypeName_returnMeetingTypeName()
    {
        $this->meetingType->expects($this->once())
                ->method("getName");
        $this->meeting->getMeetingTypeName();
    }
}

class TestableMeeting extends Meeting
{
    public $meetingType;
    public $id;
    public $name = "meeting name";
    public $startEndTime;
    public $cancelled = false;
    public $attendees;
    public $mails;
    public $notifications;
    
    function __construct()
    {
        parent::__construct();
    }
}
