<?php

namespace Notification\Domain\Model\Firm\Program\ConsultationSetup;

use Notification\Domain\Model\ {
    Firm\Program\Consultant,
    Firm\Program\ConsultationSetup,
    Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestMailNotificationForConsultant,
    Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestMailNotificationForParticipant,
    Firm\Program\Participant,
    SharedEntity\KonsultaMailMessage
};
use Resources\ {
    Application\Service\SenderInterface,
    Domain\ValueObject\DateTimeInterval
};

class ConsultationRequest
{

    /**
     *
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $participant;

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
    
    public function getFirmMailSender(): SenderInterface
    {
        return $this->consultationSetup->getFirmMailSender();
    }
    
    public function createScheduleOfferedMail(): ConsultationRequestMailNotificationForParticipant
    {
        
        $subject = "Konsulta: permintaan perubahan jadwal konsultasi";
        $greetings = "Hi peserta";
        
        $hari = $this->startEndTime->getStartDayInIndonesianFormat();
        $tanggal = $this->startEndTime->getStartTime()->format('d M Y');
        $jamMulai = $this->startEndTime->getStartTime()->format('H.i');
        $jamSelesai = $this->startEndTime->getEndTime()->format('H.i');
        $mainMessage = <<<_MESSAGE
Konsultan {$this->consultant->getPersonnelName()} telah mengajukan perubahan jadwal konsultasi mejadi:
    {$hari}, {$tanggal} jam {$jamMulai} - {$jamSelesai}
Untuk menerima, membatalkan atau mengajukan jadwal baru kunjungi:
_MESSAGE;
        $url = $this->consultationSetup->getFirmWhitelableUrl();
        $apiPath = "/consultation-requests/{$this->id}";
        
        $mailMessage = new KonsultaMailMessage($subject, $greetings, $mainMessage, $url, $apiPath);
        $participantMailNotification = $this->participant->createMailNotification();
        return new ConsultationRequestMailNotificationForParticipant($this, $participantMailNotification, $mailMessage);
    }
    
    public function createScheduleChangedMail(): ConsultationRequestMailNotificationForConsultant
    {
        $subject = "Konsulta: permintaan perubahan jadwal konsultasi";
        $greetings = "Hi konsultan";
        
        $hari = $this->startEndTime->getStartDayInIndonesianFormat();
        $tanggal = $this->startEndTime->getStartTime()->format('d M Y');
        $jamMulai = $this->startEndTime->getStartTime()->format('H.i');
        $jamSelesai = $this->startEndTime->getEndTime()->format('H.i');
        $mainMessage = <<<_MESSAGE
Peserta {$this->participant->getParticipantName()} telah mengajukan perubahan jadwal konsultasi mejadi:
    {$hari}, {$tanggal} jam {$jamMulai} - {$jamSelesai}
Untuk menerima, membatalkan atau mengajukan jadwal baru kunjungi:
_MESSAGE;
        $url = $this->consultationSetup->getFirmWhitelableUrl();
        $apiPath = "/consultation-requests/{$this->id}";
        
        $mailMessage = new KonsultaMailMessage($subject, $greetings, $mainMessage, $url, $apiPath);
        $consultantMailNotification = $this->consultant->createMailNotification();
        
        return new ConsultationRequestMailNotificationForConsultant($this, $consultantMailNotification, $mailMessage);
    }
    
    public function createProposedMail(): ConsultationRequestMailNotificationForConsultant
    {
        $subject = "Konsulta: permintaan jadwal konsultasi";
        $greetings = "Hi konsultan";
        
        $hari = $this->startEndTime->getStartDayInIndonesianFormat();
        $tanggal = $this->startEndTime->getStartTime()->format('d M Y');
        $jamMulai = $this->startEndTime->getStartTime()->format('H.i');
        $jamSelesai = $this->startEndTime->getEndTime()->format('H.i');
        $mainMessage = <<<_MESSAGE
Peserta {$this->participant->getParticipantName()} telah mengajukan permintaan jadwal konsultasi pada:
    {$hari}, {$tanggal} jam {$jamMulai} - {$jamSelesai}
Untuk menerima, membatalkan atau mengajukan jadwal baru kunjungi:
_MESSAGE;
        $url = $this->consultationSetup->getFirmWhitelableUrl();
        $apiPath = "/consultation-requests/{$this->id}";
        
        $mailMessage = new KonsultaMailMessage($subject, $greetings, $mainMessage, $url, $apiPath);
        $consultantMailNotification = $this->consultant->createMailNotification();
        
        return new ConsultationRequestMailNotificationForConsultant($this, $consultantMailNotification, $mailMessage);
        
    }
    
}
