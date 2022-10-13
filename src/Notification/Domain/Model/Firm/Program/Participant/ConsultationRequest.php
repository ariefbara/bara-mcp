<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm\Program\Consultant;
use Notification\Domain\Model\Firm\Program\Participant;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest\ConsultationRequestMail;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest\ConsultationRequestNotification;
use Notification\Domain\Model\Firm\Team\Member;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Resources\Domain\ValueObject\DateTimeInterval;
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\ConsultationRequestStatusVO;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use SharedContext\Domain\ValueObject\MailMessage;
use SharedContext\Domain\ValueObject\MailMessageBuilder;
use SharedContext\Domain\ValueObject\NotificationMessageBuilder;

class ConsultationRequest implements CanSendPersonalizeMail
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Consultant
     */
    protected $consultant;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     * 
     * @var ConsultationRequestStatusVO
     */
    protected $status;

    /**
     * 
     * @var ConsultationChannel
     */
    protected $channel;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationRequestNotifications;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationRequestMails;

    protected function __construct()
    {
        
    }

    public function createNotificationTriggeredByTeamMember(int $state, Member $submitter): void
    {

        $mentorName = $this->consultant->getPersonnelFullName();
        $memberName = $submitter->getClientFullName();
        $teamName = $this->participant->getName();
        $timeDescription = $this->startEndTime->getTimeDescriptionInIndonesianFormat();
        $media = $this->channel->getMedia();
        $location = $this->channel->getAddress();
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation-request/{$this->id}";
        $logoPath = $this->participant->getFirmLogoPath();
        $mailMessage = MailMessageBuilder::buildConsultationMailMessageForTeamMember(
                        $state, $mentorName, $memberName, $teamName, $timeDescription, $media, $location, $domain,
                        $urlPath, $logoPath);

        $this->participant->registerMailRecipient($this, $mailMessage, $submitter);

        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildConsultationNotificationMessageForTeamMember(
                        $state, $mentorName, $memberName, $teamName);
        $consultationRequestNotification = new ConsultationRequestNotification($this, $id, $message);

        $this->participant->registerNotificationRecipient($consultationRequestNotification, $submitter);
        $this->consultationRequestNotifications->add($consultationRequestNotification);

        $this->createNotificationTriggeredByParticipant($state);
    }

    public function createNotificationTriggeredByConsultant(int $state): void
    {
        $mentorName = $this->consultant->getPersonnelFullName();
        $timeDescription = $this->startEndTime->getTimeDescriptionInIndonesianFormat();
        $media = $this->channel->getMedia();
        $location = $this->channel->getAddress();
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation-request/{$this->id}";
        $logoPath = $this->participant->getFirmLogoPath();

        if ($this->status->sameValueAs(new ConsultationRequestStatusVO('offered'))) {
            $state = MailMessageBuilder::CONSULTATION_PROPOSED_BY_MENTOR;
        }
        $mailMessage = MailMessageBuilder::buildConsultationMailMessageForParticipant(
                        $state, $mentorName, $timeDescription, $media, $location, $domain, $urlPath, $logoPath);

        $this->participant->registerMailRecipient($this, $mailMessage, null);

        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildConsultationNotificationMessageForParticipant($state, $mentorName);
        $consultationRequestNotification = new ConsultationRequestNotification($this, $id, $message);
        $this->participant->registerNotificationRecipient($consultationRequestNotification, null);
        $this->consultationRequestNotifications->add($consultationRequestNotification);
    }

    public function createNotificationTriggeredByParticipant(int $state): void
    {
        $participantName = $this->participant->getName();
        $timeDescription = $this->startEndTime->getTimeDescriptionInIndonesianFormat();
        $media = $this->channel->getMedia();
        $location = $this->channel->getAddress();
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation-request/{$this->id}";
        $logoPath = $this->participant->getFirmLogoPath();

        $mailMessage = MailMessageBuilder::buildConsultationMailMessageForMentor(
                        $state, $participantName, $timeDescription, $media, $location, $domain, $urlPath, $logoPath);
        $this->consultant->registerMailRecipient($this, $mailMessage);

        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildConsultationNotificationMessageForMentor($state, $participantName);
        $consultationRequestNotification = new ConsultationRequestNotification($this, $id, $message);
        $this->consultant->registerNotificationRecipient($consultationRequestNotification);

        $this->consultationRequestNotifications->add($consultationRequestNotification);
    }

    public function addMail(MailMessage $mailMessage, string $recipientMailAddress, string $recipientName): void
    {
        $id = Uuid::generateUuid4();
        $senderMailAddress = $this->participant->getFirmMailSenderAddress();
        $senderName = $this->participant->getFirmMailSenderName();

        $consultationRequestMail = new ConsultationRequestMail(
                $this, $id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
        $this->consultationRequestMails->add($consultationRequestMail);
    }

}
