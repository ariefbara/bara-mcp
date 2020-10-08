<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\{
    Model\Firm\Program\Consultant,
    Model\Firm\Program\Participant,
    Model\Firm\Team\Member,
    SharedModel\MailMessage
};
use Resources\Domain\ValueObject\DateTimeInterval;
use Tests\TestBase;

class ConsultationRequestTest extends TestBase
{

    protected $consultationRequest;
    protected $subject, $participantGreetings, $consultantGreetings, $urlPath;
    protected $participant, $participantName = "participant name", $firmDomain = "firm@domain.com";
    protected $consultant, $consultantName = "consultant name";
    protected $startEndTime, $timeDescription = 'time description';
    protected $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participant->expects($this->any())->method("getFirmDomain")->willReturn($this->firmDomain);
        $this->participant->expects($this->any())->method("getName")->willReturn($this->participantName);

        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultant->expects($this->any())->method("getPersonnelFullName")->willReturn($this->consultantName);

        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->startEndTime->expects($this->any())->method("getTimeDescriptionInIndonesianFormat")->willReturn($this->timeDescription);

        $this->consultationRequest = new TestableConsultationRequest();
        $this->consultationRequest->participant = $this->participant;
        $this->consultationRequest->consultant = $this->consultant;
        $this->consultationRequest->startEndTime = $this->startEndTime;
        $this->consultationRequest->consultationRequestMails = new ArrayCollection();
        $this->consultationRequest->consultationRequestNotifications = new ArrayCollection();

        $this->subject = "Konsulta: Permintaan Konsultasi";
        $this->participantGreetings = "Hi Partisipan";
        $this->consultantGreetings = "Hi Konsultan";
        $this->urlPath = "/consultation-requests/{$this->consultationRequest->id}";

        $this->member = $this->buildMockOfClass(Member::class);
    }

    public function test_sendParticipantSubmittedConsultationRequestMailToOtherMember_registerParticipantAsMailRecipient()
    {
        $mainMessage = <<<_MESSAGE
Anggota tim kamu telah mengajukan permintaan konsultasi kepada konsultan {$this->consultantName} pada waktu:
    {$this->timeDescription}

Untuk mengatur waktu atau membatalkan, kunjungi:
_MESSAGE;
        $mailMessage = new MailMessage(
                $this->subject, $this->participantGreetings, $mainMessage, $this->firmDomain, $this->urlPath);
        $this->participant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationRequest, $mailMessage, $this->member);
        $this->consultationRequest->sendParticipantSubmittedConsultationRequestMailToOtherMember($this->member);
    }
    
    public function test_sendParticipantChangedConsultationRequestMailToOtherMember_registerParticipantAsMailRecipient()
    {
        $mainMessage = <<<_MESSAGE
Anggota tim kamu telah mengajukan perubahan waktu konsultasi kepada konsultan {$this->consultantName} menjadi:
    {$this->timeDescription}

Untuk mengatur waktu atau membatalkan, kunjungi:
_MESSAGE;
        $mailMessage = new MailMessage(
                $this->subject, $this->participantGreetings, $mainMessage, $this->firmDomain, $this->urlPath);
        $this->participant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationRequest, $mailMessage, $this->member);
        $this->consultationRequest->sendParticipantChangedConsultationRequestTimeMailToOtherMember($this->member);
    }
    
    public function test_sendParticipantCancelledConsultationRequestMailToOtherMember_registerParticipantAsMailRecipient()
    {
        $mainMessage = <<<_MESSAGE
Anggota tim kamu telah membatalkan pengajuan konsultasi kepada konsultan {$this->consultantName} di waktu:
    {$this->timeDescription}

Untuk melihat detail, kunjungi:
_MESSAGE;
        $mailMessage = new MailMessage(
                $this->subject, $this->participantGreetings, $mainMessage, $this->firmDomain, $this->urlPath);
        $this->participant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationRequest, $mailMessage, $this->member);
        $this->consultationRequest->sendParticipantCancelledConsultationRequestTimeMailToOtherMember($this->member);
    }

}

class TestableConsultationRequest extends ConsultationRequest
{

    public $participant;
    public $id = "consultationRequestId";
    public $consultant;
    public $startEndTime;
    public $consultationRequestNotifications;
    public $consultationRequestMails;

    function __construct()
    {
        parent::__construct();
    }

}
