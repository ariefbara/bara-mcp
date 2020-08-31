<?php

namespace Firm\Domain\Model\Firm\Program\ConsultationSetup;

use Firm\Domain\Model\Firm\Program\ {
    Consultant,
    ConsultationSetup,
    Participant
};
use Resources\ {
    Application\Service\Mailer,
    Domain\Model\Mail,
    Domain\ValueObject\DateTimeInterval
};

class ConsultationRequest
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
     * @var ConsultationSetup
     */
    protected $consultationSetup;

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
     * @var bool
     */
    protected $concluded;

    /**
     *
     * @var string||null
     */
    protected $status;
    
    protected function __construct()
    {
        ;
    }
    
    public function sendMail(Mailer $mailer): void
    {
        if ($this->status === 'proposed') {
            $this->sendProposedStatusMailToConsultant($mailer);
        } elseif ($this->status === 'offered') {
            $this->sendOfferedStatusMail($mailer);
        }
    }
    
    protected function sendProposedStatusMailToConsultant(Mailer $mailer): void
    {
        $firmWhitelableInfo = $this->consultationSetup->getFirmWhitelableInfo();
        $recipient = $this->consultant->getMailRecipient();
        
        $senderName = $firmWhitelableInfo->getMailSenderName();
        $senderAddress = $firmWhitelableInfo->getMailSenderAddress();
        
        $subject = "Konsulta: permintaan jadwal konsultasi";
        
        $body = <<<_PLAINTEXT
Hi Consultant {$recipient->getFirstName()},

Participant {$this->participant->getParticipantName()} telah mengajukan permintaan konsultasi pada waktu:
        {$this->startEndTime->getStartDayInIndonesianFormat()}, {$this->startEndTime->getStartTime()->format('d M Y')} jam {$this->startEndTime->getStartTime()->format('H.i')} s/d {$this->startEndTime->getEndTime()->format('H.i')}
Untuk menerima atau mengajukan perubahan jadwal:
{$firmWhitelableInfo->getUrl()}/personnel/program-consultations/{$this->consultationSetup->getProgramId()}/consultation-requests/{$this->id}
_PLAINTEXT;

        $alternativeBody = null;
                
        $mail = new Mail($subject, $body, $alternativeBody, $recipient);
        
        
        $mailer->send($mail, $senderName, $senderAddress);
    }
    
    protected function sendOfferedStatusMail(Mailer $mailer): void
    {
        $firmWhitelableInfo = $this->consultationSetup->getFirmWhitelableInfo();
        $recipient = $this->participant->getMailRecipient();
        
        $senderName = $firmWhitelableInfo->getMailSenderName();
        $senderAddress = $firmWhitelableInfo->getMailSenderAddress();
        
        $subject = "Konsulta: permintaan jadwal konsultasi";
        
        $body = <<<_PLAINTEXT
Hi Participant {$recipient->getFirstName()},

Konsultan {$this->consultant->getPersonnelName()} telah mengajukan perubahan jadwal konsultasi menjadi:
        {$this->startEndTime->getStartDayInIndonesianFormat()}, {$this->startEndTime->getStartTime()->format('d M Y')} jam {$this->startEndTime->getStartTime()->format('H.i')} s/d {$this->startEndTime->getEndTime()->format('H.i')}
Untuk menerima atau mengajukan jadwal lain:
{$firmWhitelableInfo->getUrl()}/client/program-participations/{$this->consultationSetup->getProgramId()}/consultation-requests/{$this->id}
_PLAINTEXT;

        $alternativeBody = null;

        $mail = new Mail($subject, $body, $alternativeBody, $recipient);

        $mailer->send($mail, $senderName, $senderAddress);
    }

}
