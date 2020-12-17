<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm\Program\Consultant;
use Notification\Domain\Model\Firm\Program\Participant;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest\ConsultationRequestMail;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest\ConsultationRequestNotification;
use Notification\Domain\Model\Firm\Team\Member;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ConsultationRequestTest extends TestBase
{

    protected $consultationRequest;
    protected $subject, $participantGreetings, $consultantGreetings, $urlPath;
    protected $participant, $participantName = "participant name", 
            $firmDomain = "firm@domain.com", $firmMailSenderAddress = "firm@domain.org", $firmMailSenderName = "firm name";
    protected $consultant, $consultantName = "consultant name";
    protected $startEndTime, $timeDescription = 'time description';
    protected $channel;
    protected $member, $memberName = "client full name";
    protected $mailMessage, $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";
    protected $state = 12;

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
        
        $this->channel = $this->buildMockOfClass(ConsultationChannel::class);

        $this->consultationRequest = new TestableConsultationRequest();
        $this->consultationRequest->participant = $this->participant;
        $this->consultationRequest->consultant = $this->consultant;
        $this->consultationRequest->startEndTime = $this->startEndTime;
        $this->consultationRequest->channel = $this->channel;
        $this->consultationRequest->consultationRequestMails = new ArrayCollection();
        $this->consultationRequest->consultationRequestNotifications = new ArrayCollection();

        $this->subject = "Konsulta: Permintaan Konsultasi";
        $this->participantGreetings = "Hi Partisipan";
        $this->consultantGreetings = "Hi Konsultan";
        $this->urlPath = "/consultation-requests/{$this->consultationRequest->id}";

        $this->member = $this->buildMockOfClass(Member::class);
        $this->member->expects($this->any())->method("getClientFullName")->willReturn($this->memberName);
        
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }
    
    protected function executeCreateNotificationTriggeredByTeamMember()
    {
        $this->consultationRequest->createNotificationTriggeredByTeamMember($this->state, $this->member);
    }
    public function test_createNotificationTriggeredByTeamMember_registerParticipanAsMailRecepient()
    {
        $this->participant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationRequest, $this->anything(), $this->member);
        $this->executeCreateNotificationTriggeredByTeamMember();
    }
    public function test_createNotificationTriggeredByTeamMember_registerConsultantAsMailRecepient()
    {
        $this->consultant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationRequest, $this->anything());
        $this->executeCreateNotificationTriggeredByTeamMember();
    }
    public function test_createNotificationTriggeredByTeamMember_registerParticipantAsNotificationRecipient()
    {
        $this->participant->expects($this->once())
                ->method("registerNotificationRecipient")
                ->with($this->anything(), $this->member);
        $this->executeCreateNotificationTriggeredByTeamMember();
    }
    public function test_createNotificationTriggeredByTeamMember_registerConsultantAsNotificationRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerNotificationRecipient")
                ->with($this->anything());
        $this->executeCreateNotificationTriggeredByTeamMember();
    }
    public function test_createNotificationTriggeredByTeamMember_addNotification()
    {
        $this->executeCreateNotificationTriggeredByTeamMember();
        $this->assertInstanceOf(ConsultationRequestNotification::class, $this->consultationRequest->consultationRequestNotifications->first());
    }
    
    protected function executeCreateNotificationTriggeredByConsultant()
    {
        $this->consultationRequest->createNotificationTriggeredByConsultant($this->state);
    }
    public function test_createNotificationTriggeredByConsultant_registerParticipanAsMailRecepient()
    {
        $this->participant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationRequest, $this->anything(), null);
        $this->executeCreateNotificationTriggeredByConsultant();
    }
    public function test_createNotificationTriggeredByConsultant_registerParticipantAsNotificationRecipient()
    {
        $this->participant->expects($this->once())
                ->method("registerNotificationRecipient")
                ->with($this->anything(), null);
        $this->executeCreateNotificationTriggeredByConsultant();
    }
    public function test_createNotificationTriggeredByConsultant_addNotification()
    {
        $this->executeCreateNotificationTriggeredByConsultant();
        $this->assertInstanceOf(ConsultationRequestNotification::class, $this->consultationRequest->consultationRequestNotifications->first());
    }
    
    protected function executeCreateNotificationTriggeredByParticipant()
    {
        $this->consultationRequest->createNotificationTriggeredByParticipant($this->state);
    }
    public function test_createNotificationTriggeredByParticipant_registerConsultantAsMailRecepient()
    {
        $this->consultant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationRequest, $this->anything());
        $this->executeCreateNotificationTriggeredByParticipant();
    }
    public function test_createNotificationTriggeredByParticipant_registerConsultantAsNotificationRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerNotificationRecipient")
                ->with($this->anything());
        $this->executeCreateNotificationTriggeredByParticipant();
    }
    public function test_createNotificationTriggeredByParticipant_addNotification()
    {
        $this->executeCreateNotificationTriggeredByParticipant();
        $this->assertInstanceOf(ConsultationRequestNotification::class, $this->consultationRequest->consultationRequestNotifications->first());
    }
    
    public function test_addMail_addConsultationRequestMailNotCollection()
    {
        $this->participant->expects($this->once())->method("getFirmMailSenderAddress");
        $this->participant->expects($this->once())->method("getFirmMailSenderName");
        $this->consultationRequest->addMail($this->mailMessage, $this->recipientMailAddress, $this->recipientName);
        $this->assertInstanceOf(ConsultationRequestMail::class, $this->consultationRequest->consultationRequestMails->first());
    }
}

class TestableConsultationRequest extends ConsultationRequest
{

    public $participant;
    public $id = "consultationRequestId";
    public $consultant;
    public $startEndTime;
    public $channel;
    public $consultationRequestNotifications;
    public $consultationRequestMails;

    function __construct()
    {
        parent::__construct();
    }

}
