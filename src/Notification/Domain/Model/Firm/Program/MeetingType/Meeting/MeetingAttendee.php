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
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotificationForAllUser;
use Resources\Uuid;
use SharedContext\Domain\ValueObject\MailMessage;
use SharedContext\Domain\ValueObject\MailMessageBuilder;
use SharedContext\Domain\ValueObject\NotificationMessageBuilder;

class MeetingAttendee implements CanSendPersonalizeMail
{

    /**
     *
     * @var Meeting
     */
    protected $meeting;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $anInitiator;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    /**
     *
     * @var ManagerMeetingAttendee|null
     */
    protected $managerAttendee;

    /**
     *
     * @var CoordinatorMeetingAttendee|null
     */
    protected $coordinatorAttendee;

    /**
     *
     * @var ConsultantMeetingAttendee|null
     */
    protected $consultantAttendee;

    /**
     *
     * @var ParticipantMeetingAttendee|null
     */
    protected $participantAttendee;

    /**
     * 
     * @var ArrayCollection
     */
    protected $mails;

    /**
     * 
     * @var ArrayCollection
     */
    protected $notifications;

    function isAnInitiator(): bool
    {
        return $this->anInitiator;
    }

    function isCancelled(): bool
    {
        return $this->cancelled;
    }

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }
    
    public function getProgramId(): string
    {
        return $this->meeting->getProgramId();
    }

    public function addInvitationSentNotification(): void
    {
        $state = MailMessageBuilder::MEETING_INVITATION_SENT;
        
        $meetingType = $this->meeting->getMeetingTypeName();
        $meetingName = $this->meeting->getName();
        $meetingDescription = $this->meeting->getDescription();
        $timeDescription = $this->meeting->getscheduleInIndonesianFormat();
        $location = $this->meeting->getLocation();
        $domain = $this->meeting->getFirmDomain();
        $urlPath = "/invitation/{$this->id}";
        $logoPath = $this->meeting->getFirmLogoPath();

        $mailMessage = MailMessageBuilder::buildMeetingMailMessage(
                        $state, $meetingType, $meetingName, $meetingDescription, $timeDescription, $location, $domain,
                        $urlPath, $logoPath);

        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildMeetingNotification($state);
        $meetingAttendeeNotification = new MeetingAttendeeNotification($this, $id, $message);
        $this->notifications->add($meetingAttendeeNotification);

        $this->registerUserAsMailRecipient($this, $mailMessage);
        $this->registerUserAsNotificationRecipient($meetingAttendeeNotification);
    }

    public function addInvitationCancelledNotification(): void
    {
        $state = MailMessageBuilder::MEETING_INVITATION_CANCELLED;
        
        $meetingType = $this->meeting->getMeetingTypeName();
        $meetingName = $this->meeting->getName();
        $meetingDescription = $this->meeting->getDescription();
        $timeDescription = $this->meeting->getscheduleInIndonesianFormat();
        $location = $this->meeting->getLocation();
        $domain = $this->meeting->getFirmDomain();
        $urlPath = "/invitation/{$this->id}";
        $logoPath = $this->meeting->getFirmLogoPath();

        $mailMessage = MailMessageBuilder::buildMeetingMailMessage(
                        $state, $meetingType, $meetingName, $meetingDescription, $timeDescription, $location, $domain,
                        $urlPath, $logoPath);

        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildMeetingNotification($state);
        $meetingAttendeeNotification = new MeetingAttendeeNotification($this, $id, $message);
        $this->notifications->add($meetingAttendeeNotification);

        $this->registerUserAsMailRecipient($this, $mailMessage);
        $this->registerUserAsNotificationRecipient($meetingAttendeeNotification);
    }

    public function registerAsMeetingMailRecipient(
            CanSendPersonalizeMail $meeting, MailMessage $mailMessage): void
    {
        $mailMessage = $mailMessage->appendUrlPath("/invitation/{$this->id}");
        $this->registerUserAsMailRecipient($meeting, $mailMessage);
    }

    public function registerAsMeetingNotificationRecipient(MeetingNotification $meetingNotification): void
    {
        $this->registerUserAsNotificationRecipient($meetingNotification);
    }

    public function addMail(MailMessage $mailMessage, string $recipientMailAddress, string $recipientName): void
    {
        $id = Uuid::generateUuid4();
        $senderMailAddress = $this->meeting->getFirmMailSenderAddress();
        $senderName = $this->meeting->getFirmMailSenderName();

        $meetingAttendeeMail = new MeetingAttendeeMail(
                $this, $id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
        $this->mails->add($meetingAttendeeMail);
    }

    protected function registerUserAsMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        if (isset($this->managerAttendee)) {
            $this->managerAttendee->registerAsMailRecipient($mailGenerator, $mailMessage);
        }
        if (isset($this->coordinatorAttendee)) {
            $this->coordinatorAttendee->registerAsMailRecipient($mailGenerator, $mailMessage);
        }
        if (isset($this->consultantAttendee)) {
            $this->consultantAttendee->registerAsMailRecipient($mailGenerator, $mailMessage);
        }
        if (isset($this->participantAttendee)) {
            $this->participantAttendee->registerAsMailRecipient($mailGenerator, $mailMessage);
        }
    }

    protected function registerUserAsNotificationRecipient(ContainNotificationForAllUser $notification): void
    {
        if (isset($this->managerAttendee)) {
            $this->managerAttendee->registerAsNotificationRecipient($notification);
        }
        if (isset($this->coordinatorAttendee)) {
            $this->coordinatorAttendee->registerAsNotificationRecipient($notification);
        }
        if (isset($this->consultantAttendee)) {
            $this->consultantAttendee->registerAsNotificationRecipient($notification);
        }
        if (isset($this->participantAttendee)) {
            $this->participantAttendee->registerAsNotificationRecipient($notification);
        }
    }

}
