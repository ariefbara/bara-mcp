<?php

namespace Notification\Domain\Model\Firm\Program\Participant\Worksheet;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm\Program\Consultant\ConsultantComment;
use Notification\Domain\Model\Firm\Program\Participant\Worksheet;
use Notification\Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentMail;
use Notification\Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentNotification;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotification;
use Resources\Uuid;
use SharedContext\Domain\ValueObject\MailMessage;
use SharedContext\Domain\ValueObject\MailMessageBuilder;
use SharedContext\Domain\ValueObject\NotificationMessageBuilder;

class Comment implements CanSendPersonalizeMail
{

    /**
     *
     * @var Worksheet
     */
    protected $worksheet;

    /**
     *
     * @var Comment|null
     */
    protected $parent;

    /**
     *
     * @var string
     */
    protected $id;
    
    /**
     *
     * @var string
     */
    protected $message = null;

    /**
     *
     * @var ConsultantComment|null
     */
    protected $consultantComment = null;

    /**
     *
     * @var ArrayCollection
     */
    protected $commentMails;

    /**
     *
     * @var ArrayCollection
     */
    protected $commentNotifications;

    protected function __construct()
    {
        
    }

    public function generateNotificationsForRepliedConsultantComment(): void
    {
        $participantName = $this->worksheet->getParticipantName();
        $missionName = $this->worksheet->getMissionName();
        $worksheetName = $this->worksheet->getName();
        $message = $this->message;
        $domain = $this->worksheet->getFirmDomain();
        $urlPath = "/participant/{$this->worksheet->getParticipantId()}/worksheet/{$this->worksheet->getId()}";
        $logoPath = $this->worksheet->getFirmLogoPath();
        
        $mailMessage = MailMessageBuilder::buildWorksheetCommentMailMessageForMentor(
                $participantName, $missionName, $worksheetName, $message, $domain, $urlPath, $logoPath);
        
        $this->parent->registerConsultantAsMailRecipient($this, $mailMessage);

        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildWorksheetCommentNotificationForMentor($participantName);

        $commentNotification = new CommentNotification($this, $id, $message);
        $this->parent->registerConsultantAsNotificationRecipient($commentNotification);

        $this->commentNotifications->add($commentNotification);
    }

    public function generateNotificationsTriggeredByConsultant(): void
    {
        $mentorName = $this->consultantComment->getConsultantName();
        $missionName = $this->worksheet->getMissionName();
        $worksheetName = $this->worksheet->getName();
        $message = $this->message;
        $domain = $this->worksheet->getFirmDomain();
        $urlPath = "";
        $logoPath = $this->worksheet->getFirmLogoPath();
        
        $mailMessage = MailMessageBuilder::buildWorksheetCommentMailMessageForParticipant(
                $mentorName, $missionName, $worksheetName, $message, $domain, $urlPath, $logoPath);
        $this->worksheet->registerParticipantAsMailRecipient($this, $mailMessage);

        $id = Uuid::generateUuid4();
        $message = NotificationMessageBuilder::buildWorksheetCommentNotificationForParticipant($mentorName);

        $commentNotification = new CommentNotification($this, $id, $message);
        $this->worksheet->registerParticipantAsNotificationRecipient($commentNotification);

        $this->commentNotifications->add($commentNotification);
    }

    public function addMail(
            MailMessage $mailMessage, string $recipientMailAddress, string $recipientName
    ): void
    {
        $id = Uuid::generateUuid4();
        $senderMailAddress = $this->worksheet->getFirmMailSenderAddress();
        $senderName = $this->worksheet->getFirmMailSenderName();

        $commentMail = new CommentMail(
                $this, $id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
        $this->commentMails->add($commentMail);
    }

    public function registerConsultantAsMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $this->consultantComment->registerConsultantAsMailRecipient($mailGenerator, $mailMessage);
    }

    public function registerConsultantAsNotificationRecipient(ContainNotification $notification): void
    {
        $this->consultantComment->registerConsultantAsNotificationRecipient($notification);
    }

    public function getConsultantName(): string
    {
        return $this->consultantComment->getConsultantName();
    }

}
