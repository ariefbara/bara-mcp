<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType\Meeting;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm\Manager\ManagerMeetingAttendee;
use Notification\Domain\Model\Firm\Program\Consultant\ConsultantMeetingAttendee;
use Notification\Domain\Model\Firm\Program\Coordinator\CoordinatorMeetingAttendee;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee\MeetingAttendeeMail;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee\MeetingAttendeeNotification;
use Notification\Domain\Model\Firm\Program\Participant\ParticipantMeetingAttendee;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class MeetingAttendeeTest extends TestBase
{
    protected $meetingAttendee;
    protected $meeting;
    protected $managerAttendee;
    protected $coordinatorAttendee;
    protected $consultantAttendee;
    protected $participantAttendee;
    protected $mailMessage;
    protected $haltPrependUrlPath = false;
    protected $meetingNotification;
    protected $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";

    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendee = new TestableMeetingAttendee();
        $this->meetingAttendee->mails = new ArrayCollection();
        $this->meetingAttendee->notifications = new ArrayCollection();
        
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        $this->meetingAttendee->meeting = $this->meeting;
        
        $this->managerAttendee = $this->buildMockOfClass(ManagerMeetingAttendee::class);
        $this->meetingAttendee->managerAttendee = $this->managerAttendee;
        $this->coordinatorAttendee = $this->buildMockOfClass(CoordinatorMeetingAttendee::class);
        $this->consultantAttendee = $this->buildMockOfClass(ConsultantMeetingAttendee::class);
        $this->participantAttendee = $this->buildMockOfClass(ParticipantMeetingAttendee::class);
        
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->meetingNotification = $this->buildMockOfClass(MeetingNotification::class);
    }
    
    protected function executeAddInvitationSentNotification()
    {
        $this->meetingAttendee->addInvitationSentNotification();
    }
    public function test_addInvitationSentNotification_addMeetingAttendeeNotificationToCollection()
    {
        $this->executeAddInvitationSentNotification();
        $this->assertEquals(1, $this->meetingAttendee->notifications->count());
        $this->assertInstanceOf(MeetingAttendeeNotification::class, $this->meetingAttendee->notifications->first());
    }
    public function test_addInvitationSentNotification_registerManagerAttendeeAsMailRecipient()
    {
        $this->managerAttendee->expects($this->once())
                ->method("registerAsMailRecipient")
                ->with($this->meetingAttendee, $this->anything());
        $this->executeAddInvitationSentNotification();
    }
    public function test_addInvitationSentNotification_registerManagerAttendeeAsNotificationRecipient()
    {
        $this->managerAttendee->expects($this->once())
                ->method("registerAsNotificationRecipient");
        $this->executeAddInvitationSentNotification();
    }
    public function test_addInvitationSentNotification_aCoordinatorAttendee_registerCoordinatorAttendeeAsMailAndNotificationRecipient()
    {
        $this->meetingAttendee->managerAttendee = null;
        $this->meetingAttendee->coordinatorAttendee = $this->coordinatorAttendee;
        
        $this->coordinatorAttendee->expects($this->once())
                ->method("registerAsMailRecipient")
                ->with($this->meetingAttendee, $this->anything());
        $this->executeAddInvitationSentNotification();
    }
    public function test_addInvitationSentNotification_registerCoordinatorAttendeeAsNotificationRecipient()
    {
        $this->meetingAttendee->managerAttendee = null;
        $this->meetingAttendee->coordinatorAttendee = $this->coordinatorAttendee;
        
        $this->coordinatorAttendee->expects($this->once())
                ->method("registerAsNotificationRecipient");
        $this->executeAddInvitationSentNotification();
    }
    public function test_addInvitationSentNotification_aConsultantAttendee_registerConsultantAttendeeAsMailAndNotificationRecipient()
    {
        $this->meetingAttendee->managerAttendee = null;
        $this->meetingAttendee->consultantAttendee = $this->consultantAttendee;
        
        $this->consultantAttendee->expects($this->once())
                ->method("registerAsMailRecipient")
                ->with($this->meetingAttendee, $this->anything());
        $this->executeAddInvitationSentNotification();
    }
    public function test_addInvitationSentNotification_registerConsultantAttendeeAsNotificationRecipient()
    {
        $this->meetingAttendee->managerAttendee = null;
        $this->meetingAttendee->consultantAttendee = $this->consultantAttendee;
        
        $this->consultantAttendee->expects($this->once())
                ->method("registerAsNotificationRecipient");
        $this->executeAddInvitationSentNotification();
    }
    public function test_addInvitationSentNotification_aParticipantAttendee_registerParticipantAttendeeAsMailAndNotificationRecipient()
    {
        $this->meetingAttendee->managerAttendee = null;
        $this->meetingAttendee->participantAttendee = $this->participantAttendee;
        
        $this->participantAttendee->expects($this->once())
                ->method("registerAsMailRecipient")
                ->with($this->meetingAttendee, $this->anything());
        $this->executeAddInvitationSentNotification();
    }
    public function test_addInvitationSentNotification_registerParticipantAttendeeAsNotificationRecipient()
    {
        $this->meetingAttendee->managerAttendee = null;
        $this->meetingAttendee->participantAttendee = $this->participantAttendee;
        
        $this->participantAttendee->expects($this->once())
                ->method("registerAsNotificationRecipient");
        $this->executeAddInvitationSentNotification();
    }
    
    protected function executeAddInvitationCancelledNotification()
    {
        $this->meetingAttendee->addInvitationCancelledNotification();
    }
    public function test_addInvitationCancelledNotification_addMeetingAttendeeNotificationToCollection()
    {
        $this->executeAddInvitationCancelledNotification();
        $this->assertEquals(1, $this->meetingAttendee->notifications->count());
        $this->assertInstanceOf(MeetingAttendeeNotification::class, $this->meetingAttendee->notifications->first());
    }
    public function test_addInvitationCancelledNotification_registerManagerAttendeeAsMailRecipient()
    {
        $this->managerAttendee->expects($this->once())
                ->method("registerAsMailRecipient")
                ->with($this->meetingAttendee, $this->anything());
        $this->executeAddInvitationCancelledNotification();
    }
    public function test_addInvitationCancelledNotification_registerManagerAttendeeAsNotificationRecipient()
    {
        $this->managerAttendee->expects($this->once())
                ->method("registerAsNotificationRecipient");
        $this->executeAddInvitationCancelledNotification();
    }
    
    protected function executeRegisterAsMeetingMailRecipient()
    {
        $this->meetingAttendee->registerAsMeetingMailRecipient($this->meeting, $this->mailMessage, $this->haltPrependUrlPath);
    }
    public function test_registerAsMeetingMailRecipient_preventMailMessageUrlPath()
    {
        $this->mailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->with("/invitations/{$this->meetingAttendee->id}");
        $this->executeRegisterAsMeetingMailRecipient();
    }
    public function test_registerAsMeetingMailRecipient_registerManagerAsMailRecipient()
    {
        $modifiedMailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->mailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->willReturn($modifiedMailMessage);
        
        $this->managerAttendee->expects($this->once())
                ->method("registerAsMailRecipient")
                ->with($this->meeting, $this->identicalTo($modifiedMailMessage));
        $this->executeRegisterAsMeetingMailRecipient();
    }
    public function test_registerAsMeetingMailRecipient_prepentUrlPrepend()
    {
        $this->haltPrependUrlPath = true;
        $this->mailMessage->expects($this->never())
                ->method("prependUrlPath");
        $this->executeRegisterAsMeetingMailRecipient();
    }
    
    public function test_registerAsMeetingNotificationRecipient_registerManagerAsNotificationRecipient()
    {
        $this->managerAttendee->expects($this->once())
                ->method("registerAsNotificationRecipient")
                ->with($this->meetingNotification);
        $this->meetingAttendee->registerAsMeetingNotificationRecipient($this->meetingNotification);
    }
    
    public function test_addMail_addMeetingAttendeeMailNotCollection()
    {
        $this->meetingAttendee->addMail($this->mailMessage, $this->recipientMailAddress, $this->recipientName);
        $this->assertEquals(1, $this->meetingAttendee->mails->count());
        $this->assertInstanceOf(MeetingAttendeeMail::class, $this->meetingAttendee->mails->first());
    }
}

class TestableMeetingAttendee extends MeetingAttendee
{
    public $meeting;
    public $id = "meeting-attendee-id";
    public $anInitiator;
    public $cancelled;
    public $managerAttendee;
    public $coordinatorAttendee;
    public $consultantAttendee;
    public $participantAttendee;
    public $mails;
    public $notifications;
    
    function __construct()
    {
        parent::__construct();
    }
}
