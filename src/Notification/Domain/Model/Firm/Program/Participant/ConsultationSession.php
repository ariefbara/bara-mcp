<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm\Program\Consultant;
use Notification\Domain\Model\Firm\Program\ConsultationSetup;
use Notification\Domain\Model\Firm\Program\Participant;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession\ConsultationSessionMail;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession\ConsultationSessionNotification;
use Notification\Domain\Model\Firm\Team\Member;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotificationforCoordinator;
use Resources\Domain\ValueObject\DateTimeInterval;
use Resources\Uuid;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use SharedContext\Domain\ValueObject\MailMessage;
use SharedContext\Domain\ValueObject\MailMessageBuilder;
use SharedContext\Domain\ValueObject\NotificationMessageBuilder;

class ConsultationSession implements CanSendPersonalizeMail
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
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     * 
     * @var ConsultationChannel
     */
    protected $channel;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessionMails;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessionNotifications;

    protected function __construct()
    {
        
    }

    public function addAcceptNotificationTriggeredByTeamMember(Member $teamMember): void
    {
        $state = MailMessageBuilder::CONSULTATION_ACCEPTED_BY_PARTICIPANT;

        $mentorName = $this->consultant->getPersonnelFullName();
        $memberName = $teamMember->getClientFullName();
        $teamName = $this->participant->getName();
        $timeDescription = $this->startEndTime->getTimeDescriptionInIndonesianFormat();
        $media = $this->channel->getMedia();
        $location = $this->channel->getAddress();
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation/{$this->id}/detail";
        $logoPath = $this->participant->getFirmLogoPath();

        $participantMailMessage = MailMessageBuilder::buildConsultationMailMessageForTeamMember(
                        $state, $mentorName, $memberName, $teamName, $timeDescription, $media, $location, $domain,
                        $urlPath, $logoPath);
        $this->participant->registerMailRecipient($this, $participantMailMessage);
        

        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildConsultationNotificationMessageForTeamMember(
                        $state, $mentorName, $memberName, $teamName);
        $consultationSessionNotification = new ConsultationSessionNotification($this, $id, $message);
        $this->participant->registerNotificationRecipient($consultationSessionNotification, $teamMember);
        $this->consultationSessionNotifications->add($consultationSessionNotification);

        $this->sendMailToMentor($state);
        $this->notifyMentor($state);
        $this->notifyAllProgramsCoordinator();
    }

    public function addAcceptNotificationTriggeredByParticipant(): void
    {
        $state = MailMessageBuilder::CONSULTATION_ACCEPTED_BY_PARTICIPANT;
        
        $mentorName = $this->consultant->getPersonnelFullName();
        $timeDescription = $this->startEndTime->getTimeDescriptionInIndonesianFormat();
        $media = $this->channel->getMedia();
        $location = $this->channel->getAddress();
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation/{$this->id}/detail";
        $logoPath = $this->participant->getFirmLogoPath();
        
        $mailMessage = MailMessageBuilder::buildConsultationMailMessageForParticipant(
                $state, $mentorName, $timeDescription, $media, $location, $domain, $urlPath, $logoPath);
        
        $this->participant->registerMailRecipient($this, $mailMessage);
        
        
        $this->sendMailToMentor($state);
        $this->notifyMentor($state);
        $this->notifyAllProgramsCoordinator();
    }

    public function addAcceptNotificationTriggeredByConsultant(): void
    {
        $state = MailMessageBuilder::CONSULTATION_ACCEPTED_BY_MENTOR;
        
        $mentorName = $this->consultant->getPersonnelFullName();
        $timeDescription = $this->startEndTime->getTimeDescriptionInIndonesianFormat();
        $media = $this->channel->getMedia();
        $location = $this->channel->getAddress();
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation/{$this->id}/detail";
        $logoPath = $this->participant->getFirmLogoPath();
        
        $mailMessage = MailMessageBuilder::buildConsultationMailMessageForParticipant(
                $state, $mentorName, $timeDescription, $media, $location, $domain, $urlPath, $logoPath);
        $this->participant->registerMailRecipient($this, $mailMessage);
        
        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildConsultationNotificationMessageForParticipant($state, $mentorName);
        $notification = new ConsultationSessionNotification($this, $id, $message);
        $this->participant->registerNotificationRecipient($notification);
        $this->consultationSessionNotifications->add($notification);
        
        $this->sendMailToMentor($state);
        $this->notifyAllProgramsCoordinator();
    }

    protected function sendMailToMentor(int $state): void
    {
        $participantName = $this->participant->getName();
        $timeDescription = $this->startEndTime->getTimeDescriptionInIndonesianFormat();
        $media = $this->channel->getMedia();
        $location = $this->channel->getAddress();
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/session/{$this->id}/detail";
        $logoPath = $this->participant->getFirmLogoPath();
        
        $mailMessage = MailMessageBuilder::buildConsultationMailMessageForMentor(
                $state, $participantName, $timeDescription, $media, $location, $domain, $urlPath, $logoPath);
        $this->consultant->registerMailRecipient($this, $mailMessage);
    }
    protected function notifyMentor(int $state): void
    {
        $participantName = $this->participant->getName();
        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildConsultationNotificationMessageForMentor($state, $participantName);
        $consultationSessionNotification = new ConsultationSessionNotification($this, $id, $message);
        $this->consultant->registerNotificationRecipient($consultationSessionNotification);
        $this->consultationSessionNotifications->add($consultationSessionNotification);
    }

    protected function notifyAllProgramsCoordinator(): void
    {
        $mentorName = $this->consultant->getPersonnelFullName();
        $participantName = $this->participant->getName();
        $timeDescription = $this->startEndTime->getTimeDescriptionInIndonesianFormat();
        $media = $this->channel->getMedia();
        $location = $this->channel->getAddress();
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/session/{$this->id}/detail";
        $logoPath = $this->participant->getFirmLogoPath();
        
        $mailMessage = MailMessageBuilder::buildConsultationMailMessageForCoordinator(
                $mentorName, $participantName, $timeDescription, $media, $location, $domain, $urlPath, $logoPath);
        
        $this->consultationSetup->registerAllCoordinatorsAsMailRecipient($this, $mailMessage);

        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildConsultationNotificationMessageForCoordinator(
                $mentorName, $participantName);
        $notification = new ConsultationSessionNotification($this, $id, $message);
        $this->consultationSetup->registerAllCoordinatorsAsNotificationRecipient($notification);
        $this->consultationSessionNotifications->add($notification);
    }

    public function addMail(MailMessage $mailMessage, string $recipientMailAddress, string $recipientName): void
    {
        $id = Uuid::generateUuid4();
        $senderMailAddress = $this->participant->getFirmMailSenderAddress();
        $senderName = $this->participant->getFirmMailSenderName();

        $consultationSessionMail = new ConsultationSessionMail(
                $this, $id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
        $this->consultationSessionMails->add($consultationSessionMail);
    }

}
