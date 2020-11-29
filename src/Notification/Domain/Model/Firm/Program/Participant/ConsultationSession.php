<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\{
    Model\Firm\Program\Consultant,
    Model\Firm\Program\Participant,
    Model\Firm\Program\Participant\ConsultationSession\ConsultationSessionMail,
    Model\Firm\Program\Participant\ConsultationSession\ConsultationSessionNotification,
    Model\Firm\Team\Member,
    SharedModel\CanSendPersonalizeMail
};
use Resources\{
    Domain\ValueObject\DateTimeInterval,
    Uuid
};
use SharedContext\Domain\ValueObject\MailMessage;

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
     * @var DateTimeInterval
     */
    protected $startEndTime;

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
        $subject = "Konsulta: Jadwal Konsultasi";
        $greetings = "Hi Partisipan";
        $mainMessage = <<<_MESSAGE
Anggota {$teamMember->getClientFullName()} dari tim {$this->participant->getName()} telah menyetujui usulan jadwal Konsultasi dari {$this->consultant->getPersonnelFullName()} di waktu:
    {$this->startEndTime->getTimeDescriptionInIndonesianFormat()}.
Untuk melihat detail konsultasi kunjungi;
_MESSAGE;
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation-sessions/$this->id";
        $logoPath = $this->participant->getFirmLogoPath();

        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);

        $this->participant->registerMailRecipient($this, $mailMessage);
        $this->consultant->registerMailRecipient($this, $this->buildMailMessageForConsultant());

        $id = Uuid::generateUuid4();
        $message = "anggota {$teamMember->getClientFullName()} dari tim {$this->participant->getName()} telah menyetujui jadwal konsultasi";
        $consultationSessionNotification = new ConsultationSessionNotification($this, $id, $message);
        $this->participant->registerNotificationRecipient($consultationSessionNotification, $teamMember);
        $this->consultant->registerNotificationRecipient($consultationSessionNotification);

        $this->consultationSessionNotifications->add($consultationSessionNotification);
    }

    public function addAcceptNotificationTriggeredByParticipant(): void
    {
        $subject = "Konsulta: Jadwal Konsultasi";
        $greetings = "Hi Partisipan";
        $mainMessage = <<<_MESSAGE
Jadwal Konsultasi bersama konsultan {$this->consultant->getPersonnelFullName()} telah disetujui di waktu:
    {$this->startEndTime->getTimeDescriptionInIndonesianFormat()}.

Untuk melihat detail konsultasi kunjungi;
_MESSAGE;
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation-sessions/$this->id";
        $logoPath = $this->participant->getFirmLogoPath();

        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);

        $this->participant->registerMailRecipient($this, $mailMessage);
        $this->consultant->registerMailRecipient($this, $this->buildMailMessageForConsultant());

        $id = Uuid::generateUuid4();
        $message = "Partisipan {$this->participant->getName()} telah menyetujui usulan jadwal konsultasi";
        $consultationSessionNotification = new ConsultationSessionNotification($this, $id, $message);
        $this->consultant->registerNotificationRecipient($consultationSessionNotification);

        $this->consultationSessionNotifications->add($consultationSessionNotification);
    }

    public function addAcceptNotificationTriggeredByConsultant(): void
    {
        $subject = "Konsulta: Jadwal Konsultasi";
        $greetings = "Hi Partisipan";
        $mainMessage = <<<_MESSAGE
Konsultan {$this->consultant->getPersonnelFullName()} telah menyetujui permintaan konsultasi di waktu:
    {$this->startEndTime->getTimeDescriptionInIndonesianFormat()}.

Untuk melihat detail konsultasi kunjungi;
_MESSAGE;
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation-sessions/$this->id";
        $logoPath = $this->participant->getFirmLogoPath();

        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);

        $this->participant->registerMailRecipient($this, $mailMessage);
        $this->consultant->registerMailRecipient($this, $this->buildMailMessageForConsultant());

        $id = Uuid::generateUuid4();
        $message = "Jadwal Konsultasi Disepakati";
        $consultationSessionNotification = new ConsultationSessionNotification($this, $id, $message);
        $this->participant->registerNotificationRecipient($consultationSessionNotification);

        $this->consultationSessionNotifications->add($consultationSessionNotification);
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

    protected function buildMailMessageForConsultant(): MailMessage
    {
        $subject = "Konsulta: Jadwal Konsultasi";
        $greetings = "Hi Konsultan";
        $mainMessage = <<<_MESSAGE
Partisipan {$this->participant->getName()} telah menyetujui usulan Konsultasi di waktu:
    {$this->startEndTime->getTimeDescriptionInIndonesianFormat()}.

Untuk melihat detail konsultasi kunjungi;
_MESSAGE;
        $domain = $this->participant->getFirmDomain();
        $urlPath = "/consultation-sessions/$this->id";
        $logoPath = $this->participant->getFirmLogoPath();
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
    }

}
