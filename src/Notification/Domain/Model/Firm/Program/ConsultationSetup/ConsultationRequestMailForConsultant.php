<?php

namespace Notification\Domain\Model\Firm\Program\ConsultationSetup;

use Notification\Domain\Model\SharedEntity\KonsultaMailRecipientInterface;
use Query\Domain\Model\FirmWhitelableInfo;
use Resources\{
    Application\Service\MailInterface,
    Domain\Model\Mail\Recipient,
    Domain\ValueObject\DateTimeInterval
};

class ConsultationRequestMailForConsultant implements MailInterface
{

    /**
     *
     * @var string
     */
    protected $subject;

    /**
     *
     * @var string
     */
    protected $senderMailAddress;

    /**
     *
     * @var string
     */
    protected $senderName;

    /**
     *
     * @var string
     */
    protected $body;

    /**
     *
     * @var Recipient
     */
    protected $recipient;

    /**
     *
     * @var string||null
     */
    protected $alternativeBody = [];

    public function __construct(string $subject, string $senderMailAddress, string $senderName, string $body,
            Recipient $recipient, ?string $alternativeBody)
    {
        $this->subject = $subject;
        $this->senderMailAddress = $senderMailAddress;
        $this->senderName = $senderName;
        $this->body = $body;
        $this->recipient = $recipient;
        $this->alternativeBody = $alternativeBody;
    }

    public static function createMailForNewConsultationRequest(
            FirmWhitelableInfo $firmWhitelableInfo, KonsultaMailRecipientInterface $recipient, string $participantName,
            DateTimeInterval $schedule, string $consultationRequestId): self
    {
        $subject = "Konsulta: permintaan konsultasi dari peserta";
        $senderMailAddress = $firmWhitelableInfo->getMailSenderAddress();
        $senderName = $firmWhitelableInfo->getMailSenderName();

        $hari = $schedule->getStartDayInIndonesianFormat();
        $tanggal = $schedule->getStartTime()->format('d M Y');
        $jamMulai = $schedule->getStartTime()->format('H.i');
        $jamSelesai = $schedule->getEndTime()->format('H.i');
        $linkUrl = "{$firmWhitelableInfo->getUrl()}/{$recipient->getRecipientUrlApi()}/consultation-requests/$consultationRequestId";

        $body = <<<_BODY
Hi konsultan {$recipient->getFirstName()},

Kamu telah menerima permintaan konsultasi dari peserta $participantName pada:
    $hari, $tanggal jam $jamMulai - $jamSelesai.
        
Untuk menerima, menolak atau menawarkan perubahan jadwal, kunjungi:
    $linkUrl
_BODY;

        $alternativeBody = null;

        return new static($subject, $senderMailAddress, $senderName, $body, $recipient, $alternativeBody);
    }

    public static function createMailForChangedSchedule(
            FirmWhitelableInfo $firmWhitelableInfo, KonsultaMailRecipientInterface $recipient, string $participantName,
            DateTimeInterval $schedule, string $consultationRequestId): self
    {
        $subject = "Konsulta: perubahan jadwal permintaan konsultasi";
        $senderMailAddress = $firmWhitelableInfo->getMailSenderAddress();
        $senderName = $firmWhitelableInfo->getMailSenderName();

        $hari = $schedule->getStartDayInIndonesianFormat();
        $tanggal = $schedule->getStartTime()->format('d M Y');
        $jamMulai = $schedule->getStartTime()->format('H.i');
        $jamSelesai = $schedule->getEndTime()->format('H.i');
        $linkUrl = "{$firmWhitelableInfo->getUrl()}/{$recipient->getRecipientUrlApi()}/consultation-requests/$consultationRequestId";

        $body = <<<_BODY
Hi konsultan {$recipient->getFirstName()},

peserta $participantName mengajukan perubahan jadwal untuk perminttan konsultasi mereka menjadi:
    $hari, $tanggal jam $jamMulai - $jamSelesai.
        
Untuk menerima, menolak atau menawarkan perubahan jadwal, kunjungi:
    $linkUrl
_BODY;

        $alternativeBody = null;

        return new static($subject, $senderMailAddress, $senderName, $body, $recipient, $alternativeBody);
    }

    public function getAlternativeBody(): ?string
    {
        return $this->$this->alternativeBody;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getDynamicAttachments()
    {
        return [];
    }

    public function getRecipients()
    {
        return [$this->recipient];
    }

    public function getSenderMailAddress(): string
    {
        return $this->senderMailAddress;
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

}
