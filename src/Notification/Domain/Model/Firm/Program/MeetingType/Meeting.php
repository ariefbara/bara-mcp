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
use SharedContext\Domain\ValueObject\MailMessageBuilder;
use SharedContext\Domain\ValueObject\NotificationMessageBuilder;

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
     * @var string|null
     */
    protected $description;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     *
     * @var string|null
     */
    protected $location;

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

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getDescription(): ?string
    {
        return $this->description;
    }

    function getLocation(): ?string
    {
        return $this->location;
    }

    protected function __construct()
    {
        
    }
    
    public function getProgramId(): string
    {
        return $this->meetingType->getProgramId();
    }

    public function addMeetingCreatedNotification(): void
    {
        $state = MailMessageBuilder::MEETING_CREATED;

        $meetingType = $this->meetingType->getName();
        $meetingName = $this->name;
        $meetingDescription = $this->description;
        $timeDescription = $this->startEndTime->getTimeDescriptionInIndonesianFormat();
        $location = $this->location;
        $domain = $this->meetingType->getFirmDomain();
        $logoPath = $this->meetingType->getFirmLogoPath();

        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildMeetingNotification($state);
        $notification = new MeetingNotification($this, $id, $message);

        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("anInitiator", true))
                ->andWhere(Criteria::expr()->eq("cancelled", false));
        $initiator = $this->attendees->matching($criteria)->first();
        if (!empty($initiator)) {
            $urlPath = "/meeting/{$this->id}";
            $mailMessage = MailMessageBuilder::buildMeetingMailMessage(
                            $state, $meetingType, $meetingName, $meetingDescription, $timeDescription, $location,
                            $domain, $urlPath, $logoPath, $icalRequired = true);

            $initiator->registerAsMeetingMailRecipient($this, $mailMessage);
            $initiator->registerAsMeetingNotificationRecipient($notification);
            $this->notifications->add($notification);
        }
    }

    public function addMeetingScheduleChangedNotification(): void
    {
        $state = MailMessageBuilder::MEETING_SCHEDULE_CHANGED;

        $meetingType = $this->meetingType->getName();
        $meetingName = $this->name;
        $meetingDescription = $this->description;
        $timeDescription = $this->startEndTime->getTimeDescriptionInIndonesianFormat();
        $location = $this->location;
        $domain = $this->meetingType->getFirmDomain();
        $urlPath = "";
        $logoPath = $this->meetingType->getFirmLogoPath();

        $mailMessage = MailMessageBuilder::buildMeetingMailMessage(
                        $state, $meetingType, $meetingName, $meetingDescription, $timeDescription, $location, $domain,
                        $urlPath, $logoPath, $icalRequired = true);

        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildMeetingNotification($state);

        $notification = New MeetingNotification($this, $id, $message);

        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("cancelled", false));
        foreach ($this->attendees->matching($criteria)->getIterator() as $attendee) {
            $attendee->registerAsMeetingMailRecipient($this, $mailMessage);
            $attendee->registerAsMeetingNotificationRecipient($notification);
        }
        $this->notifications->add($notification);
    }

    public function addMail(MailMessage $mailMessage, string $recipientMailAddress, string $recipientName): void
    {
        $id = Uuid::generateUuid4();
        $senderMailAddress = $this->meetingType->getFirmMailSenderAddress();
        $senderName = $this->meetingType->getFirmMailSenderName();

        $meetingMail = new MeetingMail(
                $this, $id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
        if ($mailMessage->isIcalRequired()) {
            $icalBuilder = new \Resources\Ical($this->id);
            $icalBuilder->setSummary($this->name)
                    ->setDtStart($this->startEndTime->getStartTime())
                    ->setDtEnd($this->startEndTime->getEndTime());
            if ($mailMessage->isIcalCancellation()) {
                $icalBuilder->setCancelled();
            }
            $meetingMail->setIcalAttachment($icalBuilder->render());
        }
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

    public function getMeetingTypeName(): string
    {
        return $this->meetingType->getName();
    }

}
