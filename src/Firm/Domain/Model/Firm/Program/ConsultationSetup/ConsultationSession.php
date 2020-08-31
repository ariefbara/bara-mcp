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

class ConsultationSession
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

    protected function __construct()
    {
        ;
    }
    
    public function sendMail(Mailer $mailer): void
    {
        $this->sendMailToConsultant($mailer);
        $this->sendMailToParticipant($mailer);
    }
    
    protected function sendMailToConsultant(Mailer $mailer): void
    {
        $whitelableInfo = $this->consultationSetup->getFirmWhitelableInfo();
        $recipient = $this->consultant->getMailRecipient();
        
        $subject = "konsulta: jadwal konsultasi";
        $body = <<<HTML_TEXT
Hi konsultan {$recipient->getFirstName()},

kamu telah mencapai kesepakatan jadwal konsultasi dengan partisipan {$this->participant->getParticipantName()} pada:
    {$this->startEndTime->getStartDayInIndonesianFormat()}, {$this->startEndTime->getStartTime()->format('d M Y')} jam {$this->startEndTime->getStartTime()->format('H.i')} s/d {$this->startEndTime->getEndTime()->format('H.i')}
Untuk detail konsultasi lebih lengkap di:
{$whitelableInfo->getUrl()}/personnel/program-consultations/{$this->consultationSetup->getProgramId()}/consultation-sessions/{$this->id}

HTML_TEXT;

        $alternativeBody = null;
        
        $mail = new Mail($subject, $body, $alternativeBody, $recipient);
        
        $senderName = $whitelableInfo->getMailSenderName();
        $senderAddress = $whitelableInfo->getMailSenderAddress();
        $mailer->send($mail, $senderName, $senderAddress);
    }
    
    protected function sendMailToParticipant(Mailer $mailer): void
    {
        $whitelableInfo = $this->consultationSetup->getFirmWhitelableInfo();
        $recipient = $this->participant->getMailRecipient();
        
        $subject = "konsulta: jadwal konsultasi";
        $body = <<<HTML_TEXT
Hi participant {$recipient->getFirstName()},

kamu telah mencapai kesepakatan jadwal konsultasi dengan konsultant {$this->consultant->getPersonnelName()} pada:
    {$this->startEndTime->getStartDayInIndonesianFormat()}, {$this->startEndTime->getStartTime()->format('d M Y')} jam {$this->startEndTime->getStartTime()->format('H.i')} s/d {$this->startEndTime->getEndTime()->format('H.i')}
Untuk detail konsultasi lebih lengkap di:
{$whitelableInfo->getUrl()}/client/program-participations/{$this->consultationSetup->getProgramId()}/consultation-sessions/{$this->id}

HTML_TEXT;

        $alternativeBody = null;
        
        $mail = new Mail($subject, $body, $alternativeBody, $recipient);
        
        $senderName = $whitelableInfo->getMailSenderName();
        $senderAddress = $whitelableInfo->getMailSenderAddress();
        $mailer->send($mail, $senderName, $senderAddress);
        
    }

}
