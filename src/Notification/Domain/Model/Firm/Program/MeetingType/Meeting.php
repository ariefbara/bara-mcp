<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Notification\Domain\Model\Firm\Program\MeetingType;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingMail;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingNotification;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Resources\Domain\ValueObject\DateTimeInterval;
use Resources\Uuid;
use SharedContext\Domain\ValueObject\MailMessage;

class Meeting implements CanSendPersonalizeMail
{

    /**
     *
     * @var MeetingType
     */
    protected $meetingType;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    /**
     * 
     * @var ArrayCollection
     */
    protected $attendees;

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

    protected function __construct()
    {
        
    }

    public function addMeetingCreatedNotification(): void
    {
        $subject = "Jadwal Meeting";
        $greetings = "Hi";
        $mainMessage = <<<_MESSAGE
Anda telah berhasi membuat jadwal meeting di waktu:
    {$this->startEndTime->getTimeDescriptionInIndonesianFormat()}.
Untuk mengundang/membatalkan peserta meeting serta mengubah jadwal, kunjungi:
_MESSAGE;
        $domain = $this->meetingType->getFirmDomain();
        $urlPath = "";
        $logoPath = $this->meetingType->getFirmLogoPath();
        
        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
        
        $id = Uuid::generateUuid4();
        $message = "meeting berhasil dibuat";
        $meetingNotification = new MeetingNotification($this, $id, $message);
        
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("anInitiator", true))
                ->andWhere(Criteria::expr()->eq("cancelled", false));
        $initiator = $this->attendees->matching($criteria)->first();
        if (!empty($initiator)) {
            $initiator->registerAsMeetingMailRecipient($this, $mailMessage);
            $initiator->registerAsMeetingNotificationRecipient($meetingNotification);
            $this->notifications->add($meetingNotification);
        }
    }

    public function addMeetingScheduleChangedNotification(): void
    {
        $subject = "Perubahan Jadwal Meeting";
        $greetings = "Hi";
        $mainMessage = <<<_MESSAGE
Jadwal Meeting {$this->name} telah berubah menjadi:
    {$this->startEndTime->getTimeDescriptionInIndonesianFormat()}.
Kunjungi link berikut untuk melihat detail meeting.
_MESSAGE;
        $domain = $this->meetingType->getFirmDomain();
        $urlPath = "";
        $logoPath = $this->meetingType->getFirmLogoPath();
        
        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
        
        $id = Uuid::generateUuid4();
        $message = "meeting schedule changed";
        $meetingNotification = new MeetingNotification($this, $id, $message);
        
        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
        
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("cancelled", false));
        foreach ($this->attendees->matching($criteria)->getIterator() as $attendee) {
            $attendee->registerAsMeetingMailRecipient($this, $mailMessage);
            $attendee->registerAsMeetingNotificationRecipient($meetingNotification);
        }
        $this->notifications->add($meetingNotification);
    }

    public function addMail(MailMessage $mailMessage, string $recipientMailAddress,
            string $recipientName): void
    {
        $id = Uuid::generateUuid4();
        $senderMailAddress = $this->meetingType->getFirmMailSenderAddress();
        $senderName = $this->meetingType->getFirmMailSenderName();
        
        $meetingMail = new MeetingMail(
                $this, $id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
        $this->mails->add($meetingMail);
    }
    
    public function getscheduleInIndonesianFormat(): string
    {
        return $this->startEndTime->getTimeDescriptionInIndonesianFormat();
    }
    
    public function getFirmDomain(): string
    {
        return $this->meetingType->getFirmDomain();
    }

    public function getFirmLogoPath(): ?string
    {
        return $this->meetingType->getFirmLogoPath();
    }

    public function getFirmMailSenderAddress(): string
    {
        return $this->meetingType->getFirmMailSenderAddress();
    }

    public function getFirmMailSenderName(): string
    {
        return $this->meetingType->getFirmMailSenderName();
    }

}
