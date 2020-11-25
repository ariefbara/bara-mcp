<?php

namespace Notification\Domain\Model\Firm\Program\Participant\Worksheet;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\ {
    Model\Firm\Program\Consultant\ConsultantComment,
    Model\Firm\Program\Participant\Worksheet,
    Model\Firm\Program\Participant\Worksheet\Comment\CommentMail,
    Model\Firm\Program\Participant\Worksheet\Comment\CommentNotification,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification
};
use Resources\Uuid;
use SharedContext\Domain\ValueObject\MailMessage;

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
        $subject = "Konsulta: Komentar Worksheet";
        $greetings = "Hi Konsultan";
        $mainMessage = "Partisipan {$this->worksheet->getParticipantName()} telah membalas komentar";
        $domain = $this->worksheet->getFirmDomain();
        $urlPath = "/worksheets/{$this->worksheet->getId()}/comments/{$this->id}";
        $logoPath = $this->worksheet->getFirmLogoPath();
        
        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
        
        $this->parent->registerConsultantAsMailRecipient($this, $mailMessage);

        $id = Uuid::generateUuid4();
        $message = "partisipan {$this->worksheet->getParticipantName()} telah membalas komentar";

        $commentNotification = new Comment\CommentNotification($this, $id, $message);
        $this->parent->registerConsultantAsNotificationRecipient($commentNotification);

        $this->commentNotifications->add($commentNotification);
    }

    public function generateNotificationsTriggeredByConsultant(): void
    {
        $subject = "Konsulta: Komentar Worksheet";
        $greetings = "Hi Participan";
        $mainMessage = "Konsultant {$this->consultantComment->getConsultantName()} telah memberi komentar di worksheet.";
        $domain = $this->worksheet->getFirmDomain();
        $urlPath = "/comments/{$this->id}";
        $logoPath = $this->worksheet->getFirmLogoPath();
        
        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
        $this->worksheet->registerParticipantAsMailRecipient($this, $mailMessage);

        $id = Uuid::generateUuid4();
        $message = "comment submitted";

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
