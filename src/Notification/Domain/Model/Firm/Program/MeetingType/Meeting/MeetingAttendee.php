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
use Resources\Domain\ValueObject\DateTimeInterval;
use Resources\Uuid;
use SharedContext\Domain\ValueObject\MailMessage;

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

    protected function __construct()
    {
        
    }

    public function addInvitationSentNotification(): void
    {
        $subject = "Undangan Meeting";
        $greetings = "Hi";
        $mainMessage = <<<_MESSAGE
Anda telah menerima undangan meeting pada waktu:
    {$this->meeting->getscheduleInIndonesianFormat()}
Kunjungi link berikut untuk melihat detail.
_MESSAGE;
        $domain = $this->meeting->getFirmDomain();
        $urlPath = "/meeting-invitations/{$this->id}";
        $logoPath = $this->meeting->getFirmLogoPath();

        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);

        $id = Uuid::generateUuid4();
        $message = "meeting invitation received";
        $meetingAttendeeNotification = new MeetingAttendeeNotification($this, $id, $message);
        $this->notifications->add($meetingAttendeeNotification);

        $this->registerUserAsMailRecipient($this, $mailMessage);
        $this->registerUserAsNotificationRecipient($meetingAttendeeNotification);
    }

    public function addInvitationCancelledNotification(): void
    {
        $subject = "Undangan Meeting Dibatalkan";
        $greetings = "Hi";
        $mainMessage = <<<_MESSAGE
Undangan meeting pada waktu:
    {$this->meeting->getscheduleInIndonesianFormat()}
telah dibatalkan.
Kunjungi link berikut untuk melihat detail.
_MESSAGE;
        $domain = $this->meeting->getFirmDomain();
        $urlPath = "/meeting-invitations/{$this->id}";
        $logoPath = $this->meeting->getFirmLogoPath();

        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);

        $id = Uuid::generateUuid4();
        $message = "meeting invitation has been cancelled";
        $meetingAttendeeNotification = new MeetingAttendeeNotification($this, $id, $message);
        $this->notifications->add($meetingAttendeeNotification);

        $this->registerUserAsMailRecipient($this, $mailMessage);
        $this->registerUserAsNotificationRecipient($meetingAttendeeNotification);
    }

    public function registerAsMeetingMailRecipient(CanSendPersonalizeMail $meeting, MailMessage $mailMessage): void
    {
        $modifiedMailMessage = $mailMessage->prependUrlPath("/invitations/{$this->id}");
        $this->registerUserAsMailRecipient($meeting, $modifiedMailMessage);
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
