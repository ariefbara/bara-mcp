<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\{
    Model\Firm\Program\Consultant,
    Model\Firm\Program\Participant,
    Model\Firm\Team\Member,
    SharedModel\canSendPersonalizeMail,
    SharedModel\MailMessage
};
use Resources\Domain\ValueObject\DateTimeInterval;

class ConsultationRequest implements canSendPersonalizeMail
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
     * @var ArrayCollection
     */
    protected $consultationRequestNotifications;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationRequestMails;

    protected const SUBMITTED_BY_PARTICIPANT = 1;
    protected const TIME_CHANGED_BY_PARTICIPANT = 2;
    protected const CANCELLED_BY_PARTICIPANT = 3;
    protected const OFFERED_BY_CONSULTANT = 4;
    protected const REJECTED_BY_CONSULTANT = 5;

    protected function __construct()
    {
        ;
    }

    protected function buildMailMessageForParticipant(int $activity): MailMessage
    {
        switch ($activity) {
            case self::SUBMITTED_BY_PARTICIPANT:
                $introduction = "Anggota tim kamu telah mengajukan permintaan konsultasi kepada konsultan {$this->consultant->getPersonnelFullName()} pada waktu:";
                $closing = "Untuk mengatur waktu atau membatalkan, kunjungi:";
                break;
            case self::TIME_CHANGED_BY_PARTICIPANT:
                $introduction = "Anggota tim kamu telah mengajukan perubahan waktu konsultasi kepada konsultan {$this->consultant->getPersonnelFullName()} menjadi:";
                $closing = "Untuk mengatur waktu atau membatalkan, kunjungi:";
                break;
            case self::CANCELLED_BY_PARTICIPANT:
                $introduction = "Anggota tim kamu telah membatalkan pengajuan konsultasi kepada konsultan {$this->consultant->getPersonnelFullName()} di waktu:";
                $closing = "Untuk melihat detail, kunjungi:";
                break;
            case self::OFFERED_BY_CONSULTANT:
                $introduction = "Konsultan {$this->consultant->getPersonnelFullName()} telah mengajukan perubahan waktu konsultasi menjadi:";
                $closing = "Untuk menerima, mengatur waktu atau membatalkan, kunjungi:";
                break;
            case self::REJECTED_BY_CONSULTANT:
                $introduction = "Konsultan {$this->consultant->getPersonnelFullName()} telah permintaan konsultasi di waktu:";
                $closing = "Untuk melihat detail, kunjungi:";
                break;
        }
        $subject = "Konsulta: Permintaan Konsultasi";
        $greetings = "Hi Partisipan";
        $mainMessage = <<<_MESSAGE
{$introduction}
    {$this->startEndTime->getTimeDescriptionInIndonesianFormat()}

{$closing}
_MESSAGE;
        $firmDomain = $this->participant->getFirmDomain();
        $urlPath = "/consultation-requests/{$this->id}";
        return new MailMessage($subject, $greetings, $mainMessage, $firmDomain, $urlPath);
    }

    public function sendParticipantSubmittedConsultationRequestMailToOtherMember(Member $submitter): void
    {
        $this->participant->registerMailRecipient(
                $this, $this->buildMailMessageForParticipant(self::SUBMITTED_BY_PARTICIPANT), $submitter);
    }

    public function sendParticipantChangedConsultationRequestTimeMailToOtherMember(Member $submitter): void
    {
        $this->participant->registerMailRecipient(
                $this, $this->buildMailMessageForParticipant(self::TIME_CHANGED_BY_PARTICIPANT), $submitter);
    }

    public function sendParticipantCancelledConsultationRequestTimeMailToOtherMember(Member $submitter): void
    {
        $this->participant->registerMailRecipient(
                $this, $this->buildMailMessageForParticipant(self::CANCELLED_BY_PARTICIPANT), $submitter);
    }

    public function sendParticipantSubmittedConsultationRequestMailToConsultant(): void
    {
        
    }

    public function sendParticipantChangedConsultationRequestTimeMailToConsultant(): void
    {
        
    }

    public function sendParticipantCancelledConsultationRequestTimeMailToConsultant(): void
    {
        
    }

    public function sendConsultantOfferedConsultationRequestMail(): void
    {
        
    }

    public function sendConsultantRejectedConsultationRequestMail(): void
    {
        
    }

    public function addMail(MailMessage $mailMessage, string $recipientMailAddress, string $recipientName): void
    {
//        $id = Uuid::generateUuid4();
//        $senderMailAddress = $this->participant->getFirmMailSenderAddress();
//        $senderName = $this->participant->getFirmMailSenderName();
//        $subject = $mailMessage->getSubject();
//        $message = $mailMessage->getTextMessage();
//        $htmlMessage = $mailMessage->getHtmlMessage();
//
//        $consultationRequestMail = new ConsultationRequestMail(
//                $this, $id, $senderMailAddress, $senderName, $subject, $message, $htmlMessage, $recipientMailAddress,
//                $recipientName);
//        $this->consultationRequestMails->add($consultationRequestMail);
    }

}
