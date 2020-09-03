<?php

namespace Notification\Domain\Model\Firm\Program\ConsultationSetup;

use DateTimeImmutable;
use Notification\Domain\Model\ {
    Firm\Program\Consultant,
    Firm\Program\Consultant\ConsultantMailNotification,
    Firm\Program\ConsultationSetup,
    Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestMailNotificationForConsultant,
    Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestMailNotificationForParticipant,
    Firm\Program\Participant,
    Firm\Program\Participant\ParticipantMailNotification,
    SharedEntity\KonsultaMailMessage
};
use Resources\Domain\ValueObject\DateTimeInterval;
use Tests\TestBase;

class ConsultationRequestTest extends TestBase
{

    protected $consultationRequest;
    protected $consultationSetup;
    protected $participant;
    protected $consultant;
    protected $startEndTime, $startTime, $endTime;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = new TestableConsultationRequest();
        $this->consultationRequest->id = 'consultationRequestId';

        $this->startTime = new DateTimeImmutable('+1 hours');
        $this->endTime = new DateTimeImmutable('+2 hours');
        $this->startEndTime = new DateTimeInterval($this->startTime, $this->endTime);
        $this->consultationRequest->startEndTime = $this->startEndTime;

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationRequest->consultationSetup = $this->consultationSetup;

        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->consultationRequest->participant = $this->participant;

        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultationRequest->consultant = $this->consultant;
    }

    public function test_getFirmMailSender_returnConsultationSetupsGetFirmMailSenderResult()
    {
        $this->consultationSetup->expects($this->once())
                ->method('getFirmMailSender');
        $this->consultationRequest->getFirmMailSender();
    }

    public function test_createScheduleOfferedMail_returnConsultationRequestMailNotificationForParticipant()
    {
        $participantMailNotification = $this->buildMockOfClass(ParticipantMailNotification::class);
        $this->participant->expects($this->once())
                ->method('createMailNotification')
                ->willReturn($participantMailNotification);
        
        $subject = "Konsulta: permintaan perubahan jadwal konsultasi";
        $greetings = "Hi peserta";
        
        $personnelName = 'personnel name';
        $this->consultant->expects($this->once())
                ->method('getPersonnelName')
                ->willReturn($personnelName);
        
        $hari = $this->startEndTime->getStartDayInIndonesianFormat();
        $tanggal = $this->startEndTime->getStartTime()->format('d M Y');
        $jamMulai = $this->startEndTime->getStartTime()->format('H.i');
        $jamSelesai = $this->startEndTime->getEndTime()->format('H.i');
        $mainMessage = <<<_MESSAGE
Konsultan {$personnelName} telah mengajukan perubahan jadwal konsultasi mejadi:
    {$hari}, {$tanggal} jam {$jamMulai} - {$jamSelesai}
Untuk menerima, membatalkan atau mengajukan jadwal baru kunjungi:
_MESSAGE;
    
        $url = "https://firm.com/konsulta";
        $this->consultationSetup->expects($this->once())
                ->method('getFirmWhitelableUrl')
                ->willReturn($url);
        $apiPath = "/consultation-requests/{$this->consultationRequest->id}";
        
        $mailMessage = new KonsultaMailMessage($subject, $greetings, $mainMessage, $url, $apiPath);

        $consultationRequestMailNotificationForParticipant = new ConsultationRequestMailNotificationForParticipant(
                $this->consultationRequest, $participantMailNotification, $mailMessage);
        $this->assertEquals($consultationRequestMailNotificationForParticipant, $this->consultationRequest->createScheduleOfferedMail());
    }
    
    public function test_createScheduleChangedMail_returnConsultationRequestMailNotificationForConsultant()
    {
        $consultantMailNotification = $this->buildMockOfClass(ConsultantMailNotification::class);
        $this->consultant->expects($this->once())
                ->method('createMailNotification')
                ->willReturn($consultantMailNotification);
        
        $subject = "Konsulta: permintaan perubahan jadwal konsultasi";
        $greetings = "Hi konsultan";
        
        $participantName = 'participant name';
        $this->participant->expects($this->once())
                ->method('getParticipantName')
                ->willReturn($participantName);
        
        $hari = $this->startEndTime->getStartDayInIndonesianFormat();
        $tanggal = $this->startEndTime->getStartTime()->format('d M Y');
        $jamMulai = $this->startEndTime->getStartTime()->format('H.i');
        $jamSelesai = $this->startEndTime->getEndTime()->format('H.i');
        $mainMessage = <<<_MESSAGE
Peserta {$participantName} telah mengajukan perubahan jadwal konsultasi mejadi:
    {$hari}, {$tanggal} jam {$jamMulai} - {$jamSelesai}
Untuk menerima, membatalkan atau mengajukan jadwal baru kunjungi:
_MESSAGE;
    
        $url = "https://firm.com/konsulta";
        $this->consultationSetup->expects($this->once())
                ->method('getFirmWhitelableUrl')
                ->willReturn($url);
        $apiPath = "/consultation-requests/{$this->consultationRequest->id}";
        
        $mailMessage = new KonsultaMailMessage($subject, $greetings, $mainMessage, $url, $apiPath);

        $consultationRequestMailNotificationForConsultant= new ConsultationRequestMailNotificationForConsultant(
                $this->consultationRequest, $consultantMailNotification, $mailMessage);
        $this->assertEquals($consultationRequestMailNotificationForConsultant, $this->consultationRequest->createScheduleChangedMail());
    }
    
    public function test_createProposedMail_returnConsultationRequestMailNotificationForConsultant()
    {
        $consultantMailNotification = $this->buildMockOfClass(ConsultantMailNotification::class);
        $this->consultant->expects($this->once())
                ->method('createMailNotification')
                ->willReturn($consultantMailNotification);
        
        $subject = "Konsulta: permintaan jadwal konsultasi";
        $greetings = "Hi konsultan";
        
        $participantName = 'participant name';
        $this->participant->expects($this->once())
                ->method('getParticipantName')
                ->willReturn($participantName);
        
        $hari = $this->startEndTime->getStartDayInIndonesianFormat();
        $tanggal = $this->startEndTime->getStartTime()->format('d M Y');
        $jamMulai = $this->startEndTime->getStartTime()->format('H.i');
        $jamSelesai = $this->startEndTime->getEndTime()->format('H.i');
        $mainMessage = <<<_MESSAGE
Peserta {$participantName} telah mengajukan permintaan jadwal konsultasi pada:
    {$hari}, {$tanggal} jam {$jamMulai} - {$jamSelesai}
Untuk menerima, membatalkan atau mengajukan jadwal baru kunjungi:
_MESSAGE;
    
        $url = "https://firm.com/konsulta";
        $this->consultationSetup->expects($this->once())
                ->method('getFirmWhitelableUrl')
                ->willReturn($url);
        $apiPath = "/consultation-requests/{$this->consultationRequest->id}";
        
        $mailMessage = new KonsultaMailMessage($subject, $greetings, $mainMessage, $url, $apiPath);

        $consultationRequestMailNotificationForConsultant= new ConsultationRequestMailNotificationForConsultant(
                $this->consultationRequest, $consultantMailNotification, $mailMessage);
        $this->assertEquals($consultationRequestMailNotificationForConsultant, $this->consultationRequest->createProposedMail());
    }

}

class TestableConsultationRequest extends ConsultationRequest
{

    public $consultationSetup;
    public $id;
    public $participant;
    public $consultant;
    public $startEndTime;
    public $concluded;
    public $status;

    function __construct()
    {
        parent::__construct();
    }

}
