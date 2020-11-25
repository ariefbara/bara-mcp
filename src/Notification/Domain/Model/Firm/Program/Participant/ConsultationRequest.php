<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\{
    Model\Firm\Program\Consultant,
    Model\Firm\Program\Participant,
    Model\Firm\Program\Participant\ConsultationRequest\ConsultationRequestMail,
    Model\Firm\Program\Participant\ConsultationRequest\ConsultationRequestNotification,
    Model\Firm\Team\Member,
    SharedModel\CanSendPersonalizeMail
};
use Resources\{
    Domain\ValueObject\DateTimeInterval,
    Uuid
};
use SharedContext\Domain\ValueObject\MailMessage;

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
     * @var ArrayCollection
     */
    protected $consultationRequestNotifications;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationRequestMails;

    const SUBMITTED_BY_PARTICIPANT = 1;
    const TIME_CHANGED_BY_PARTICIPANT = 2;
    const CANCELLED_BY_PARTICIPANT = 3;
    const OFFERED_BY_CONSULTANT = 4;
    const REJECTED_BY_CONSULTANT = 5;
    const NOTIFICATION_MESSAGE = [
        self::SUBMITTED_BY_PARTICIPANT => "consultation request submitted",
        self::TIME_CHANGED_BY_PARTICIPANT => "consultation request time changed",
        self::CANCELLED_BY_PARTICIPANT => "consultation request cancelled",
        self::OFFERED_BY_CONSULTANT => "consultation request offered",
        self::REJECTED_BY_CONSULTANT => "consultation request rejected",
    ];

    protected function __construct()
    {
        
    }

    protected function buildNotificationMessageTriggeredByTeamMember(int $state, string $submitterName): string
    {
        switch ($state) {
            case self::SUBMITTED_BY_PARTICIPANT:
                return "anggota $submitterName dari tim {$this->participant->getName()} telah mengajukan permintaan konsultasi";
            case self::TIME_CHANGED_BY_PARTICIPANT:
                return "anggota $submitterName dari tim {$this->participant->getName()} telah mengajukan perubahan waktu konsultasi";
            case self::CANCELLED_BY_PARTICIPANT:
                return "anggota $submitterName dari tim {$this->participant->getName()} telah membatalkan permintaan konsultasi";
        }
    }

    protected function buildNotificationMessageTriggeredByConsultant(int $state): string
    {
        switch ($state) {
            case self::OFFERED_BY_CONSULTANT:
                return "konsultan {$this->consultant->getPersonnelFullName()} telah mengajukan perubahan waktu konsultasi";
            case self::REJECTED_BY_CONSULTANT:
                return "konsultan {$this->consultant->getPersonnelFullName()} telah menolak permintaan konsultasi";
        }
    }

    protected function buildNotificationMessageTriggeredByParticipant(int $state): string
    {
        switch ($state) {
            case self::SUBMITTED_BY_PARTICIPANT:
                return "partisipan {$this->participant->getName()} telah mengajukan permintaan konsultasi";
            case self::TIME_CHANGED_BY_PARTICIPANT:
                return "partisipan {$this->participant->getName()} telah mengajukan perubahan waktu konsultasi";
            case self::CANCELLED_BY_PARTICIPANT:
                return "partisipan {$this->participant->getName()} telah membatalkan permintaan konsultasi";
        }
    }

    protected function buildMailMessageTriggeredByConsultant(int $state): MailMessage
    {
        switch ($state) {
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
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation-requests/{$this->id}";
        $logoPath = $this->participant->getFirmLogoPath();
        return new MailMessage($subject, $greetings, $closing, $domain, $urlPath, $logoPath);
    }

    protected function buildMailMessageTriggeredByTeamMember(int $state, string $submitterName): MailMessage
    {
        switch ($state) {
            case self::SUBMITTED_BY_PARTICIPANT:
                $introduction = "Anggota {$submitterName} dari tim {$this->participant->getName()} telah mengajukan permintaan konsultasi kepada konsultan {$this->consultant->getPersonnelFullName()} pada waktu:";
                $closing = "Untuk mengatur waktu atau membatalkan, kunjungi:";
                break;
            case self::TIME_CHANGED_BY_PARTICIPANT:
                $introduction = "Anggota {$submitterName} dari tim {$this->participant->getName()} telah mengajukan perubahan waktu konsultasi kepada konsultan {$this->consultant->getPersonnelFullName()} menjadi:";
                $closing = "Untuk mengatur waktu atau membatalkan, kunjungi:";
                break;
            case self::CANCELLED_BY_PARTICIPANT:
                $introduction = "Anggota {$submitterName} dari tim {$this->participant->getName()} telah membatalkan permintaan konsultasi kepada konsultan {$this->consultant->getPersonnelFullName()} di waktu:";
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
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation-requests/{$this->id}";
        $logoPath = $this->participant->getFirmLogoPath();
        return new MailMessage($subject, $greetings, $closing, $submitterName, $urlPath, $logoPath);
    }

    protected function buildMailMessageTriggeredByParticipant(int $state): MailMessage
    {
        switch ($state) {
            case self::SUBMITTED_BY_PARTICIPANT:
                $introduction = "Partisipan {$this->participant->getName()} telah mengajukan permintaan konsultasi di waktu:";
                $closing = "Untuk menerima, mengajukan perubahan waktu atau menolak, kunjungi:";
                break;
            case self::TIME_CHANGED_BY_PARTICIPANT:
                $introduction = "Partisipan {$this->participant->getName()} telah mengajukan perubahan waktu konsultasi menjadi:";
                $closing = "Untuk menerima, mengajukan perubahan waktu atau menolak, kunjungi:";
                break;
            case self::CANCELLED_BY_PARTICIPANT:
                $introduction = "Partisipan {$this->participant->getName()} telah membatalkan permintaan konsultasi di waktu:";
                $closing = "Untuk melihat detail, kunjungi:";
                break;
        }
        $subject = "Konsulta: Permintaan Konsultasi";
        $greetings = "Hi Konsultan";
        $mainMessage = <<<_MESSAGE
{$introduction}
    {$this->startEndTime->getTimeDescriptionInIndonesianFormat()}

{$closing}
_MESSAGE;
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation-requests/{$this->id}";
        $logoPath = $this->participant->getFirmLogoPath();
        return new MailMessage($subject, $greetings, $closing, $domain, $urlPath, $logoPath);
    }

    public function createNotificationTriggeredByTeamMember(int $state, Member $submitter): void
    {
        $validState = [
            self::SUBMITTED_BY_PARTICIPANT,
            self::TIME_CHANGED_BY_PARTICIPANT,
            self::CANCELLED_BY_PARTICIPANT
        ];
        if (!in_array($state, $validState)) {
            return;
        }

        $submitterName = $submitter->getClientFullName();

        $mailMessage = $this->buildMailMessageTriggeredByTeamMember($state, $submitterName);
        $this->consultant->registerMailRecipient($this, $this->buildMailMessageTriggeredByParticipant($state));
        $this->participant->registerMailRecipient($this, $mailMessage, $submitter);

        $id = Uuid::generateUuid4();
        $message = $this->buildNotificationMessageTriggeredByTeamMember($state, $submitterName);
        $consultationRequestNotification = new ConsultationRequestNotification($this, $id, $message);

        $this->participant->registerNotificationRecipient($consultationRequestNotification, $submitter);
        $this->consultant->registerNotificationRecipient($consultationRequestNotification);
        $this->consultationRequestNotifications->add($consultationRequestNotification);
    }

    public function createNotificationTriggeredByConsultant(int $state): void
    {
        $validState = [
            self::OFFERED_BY_CONSULTANT,
            self::REJECTED_BY_CONSULTANT,
        ];
        if (!in_array($state, $validState)) {
            return;
        }
        $mailMessage = $this->buildMailMessageTriggeredByConsultant($state);
        $this->participant->registerMailRecipient($this, $mailMessage, null);

        $id = Uuid::generateUuid4();
        $message = self::NOTIFICATION_MESSAGE[$state];
        $consultationRequestNotification = new ConsultationRequestNotification($this, $id, $message);
        $this->participant->registerNotificationRecipient($consultationRequestNotification, null);
        $this->consultationRequestNotifications->add($consultationRequestNotification);
    }

    public function createNotificationTriggeredByParticipant(int $state): void
    {
        $validState = [
            self::SUBMITTED_BY_PARTICIPANT,
            self::TIME_CHANGED_BY_PARTICIPANT,
            self::CANCELLED_BY_PARTICIPANT,
        ];
        if (!in_array($state, $validState)) {
            return;
        }

        $mailMessage = $this->buildMailMessageTriggeredByParticipant($state);
//        $this->participant->registerMailRecipient($this, $mailMessage, null);
        $this->consultant->registerMailRecipient($this, $mailMessage);

        $id = Uuid::generateUuid4();
        $message = $this->buildNotificationMessageTriggeredByParticipant($state);

        $consultationRequestNotification = new ConsultationRequestNotification($this, $id, $message);
//        $this->participant->registerNotificationRecipient($consultationRequestNotification, null);
        $this->consultant->registerNotificationRecipient($consultationRequestNotification);

        $this->consultationRequestNotifications->add($consultationRequestNotification);
    }

    public function addMail(MailMessage $mailMessage, string $recipientMailAddress, string $recipientName): void
    {
        $id = Uuid::generateUuid4();
        $senderMailAddress = $this->participant->getFirmMailSenderAddress();
        $senderName = $this->participant->getFirmMailSenderName();
        $subject = $mailMessage->getSubject();
        $message = $mailMessage->getTextMessage();
        $htmlMessage = $mailMessage->getHtmlMessage();

        $consultationRequestMail = new ConsultationRequestMail(
                $this, $id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
        $this->consultationRequestMails->add($consultationRequestMail);
    }

}
