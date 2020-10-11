<?php

namespace Notification\Domain\Model\Firm\Program\Participant\Worksheet;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\{
    Model\Firm\Program\Consultant\ConsultantComment,
    Model\Firm\Program\Participant\Worksheet,
    Model\Firm\Program\Participant\Worksheet\Comment\CommentMail,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification,
    SharedModel\MailMessage
};
use Resources\Uuid;

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
        $mainMessage = <<<_MESSAGE
Partisipan {$this->worksheet->getParticipantName()} telah membalas komentar.

Untuk melihat detail komentar balasan, kunjungi:
_MESSAGE;
        $domain = $this->worksheet->getFirmDomain();
        $urlPath = "/worksheets/{$this->worksheet->getId()}/comments/{$this->id}";

        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath);
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
        $mainMessage = <<<_MESSAGE
Konsultant {$this->parent->getConsultantName()} telah memberi komentar di worksheet.

Untuk melihat detail komentar, kunjungi:
_MESSAGE;
        $domain = $this->worksheet->getFirmDomain();
        $urlPath = "/comments/{$this->id}";

        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath);
        $this->worksheet->registerParticipantAsMailRecipient($this, $mailMessage);

        $id = Uuid::generateUuid4();
        $message = "konsultan {$this->parent->getConsultantName()} telah memberi komentar di worksheet";

        $commentNotification = new Comment\CommentNotification($this, $id, $message);
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
        $subject = $mailMessage->getSubject();
        $message = $mailMessage->getTextMessage();
        $htmlMessage = $mailMessage->getHtmlMessage();

        $commentMail = new CommentMail(
                $this, $id, $senderMailAddress, $senderName, $subject, $message, $htmlMessage, $recipientMailAddress,
                $recipientName);
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
