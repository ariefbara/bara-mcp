<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm\ {
    Program\Consultant,
    Program\Participant,
    Program\Participant\ConsultationRequest\ConsultationRequestMail,
    Program\Participant\ConsultationRequest\ConsultationRequestNotification,
    Team\Member
};
use Resources\Domain\ValueObject\DateTimeInterval;
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
    protected $member, $memberName = "client full name";
    protected $mailMessage, $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";

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
        $this->member->expects($this->any())->method("getClientFullName")->willReturn($this->memberName);
        
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }
    
    protected function executeCreateNotificationTriggeredByTeamMember(int $state)
    {
        $this->consultationRequest->createNotificationTriggeredByTeamMember($state, $this->member);
    }
    public function test_createNotificationTriggeredByTeamMember_registerParticipanAsMailRecepient()
    {
        $this->participant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationRequest, $this->anything(), $this->member);
        $this->executeCreateNotificationTriggeredByTeamMember(ConsultationRequest::SUBMITTED_BY_PARTICIPANT);
    }
    public function test_createNotificationTriggeredByTeamMember_registerConsultantAsMailRecepient()
    {
        $this->consultant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationRequest, $this->anything());
        $this->executeCreateNotificationTriggeredByTeamMember(ConsultationRequest::SUBMITTED_BY_PARTICIPANT);
    }
    public function test_createNotificationTriggeredByTeamMember_registerParticipantAsNotificationRecipient()
    {
        $this->participant->expects($this->once())
                ->method("registerNotificationRecipient")
                ->with($this->anything(), $this->member);
        $this->executeCreateNotificationTriggeredByTeamMember(ConsultationRequest::SUBMITTED_BY_PARTICIPANT);
    }
    public function test_createNotificationTriggeredByTeamMember_registerConsultantAsNotificationRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerNotificationRecipient")
                ->with($this->anything());
        $this->executeCreateNotificationTriggeredByTeamMember(ConsultationRequest::SUBMITTED_BY_PARTICIPANT);
    }
    public function test_createNotificationTriggeredByTeamMember_addNotification()
    {
        $this->executeCreateNotificationTriggeredByTeamMember(ConsultationRequest::SUBMITTED_BY_PARTICIPANT);
        $this->assertInstanceOf(ConsultationRequestNotification::class, $this->consultationRequest->consultationRequestNotifications->first());
    }
    public function test_createNotificationTriggeredByTeamMember_invalidState_doNothing()
    {
        $this->participant->expects($this->never())->method("registerMailRecipient");
        $this->executeCreateNotificationTriggeredByTeamMember(ConsultationRequest::REJECTED_BY_CONSULTANT);
    }
    public function test_createNotificationTriggeredByTeamMember_timeChangedByParticipantState_registerRecipient()
    {
        $this->participant->expects($this->once())->method("registerMailRecipient");
        $this->executeCreateNotificationTriggeredByTeamMember(ConsultationRequest::TIME_CHANGED_BY_PARTICIPANT);
    }
    public function test_createNotificationTriggeredByTeamMember_cancelledParticipantState_registerRecipient()
    {
        $this->participant->expects($this->once())->method("registerMailRecipient");
        $this->executeCreateNotificationTriggeredByTeamMember(ConsultationRequest::CANCELLED_BY_PARTICIPANT);
    }
    
    protected function executeCreateNotificationTriggeredByConsultant(int $state)
    {
        $this->consultationRequest->createNotificationTriggeredByConsultant($state);
    }
    public function test_createNotificationTriggeredByConsultant_registerParticipanAsMailRecepient()
    {
        $this->participant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationRequest, $this->anything(), null);
        $this->executeCreateNotificationTriggeredByConsultant(ConsultationRequest::OFFERED_BY_CONSULTANT);
    }
    public function test_createNotificationTriggeredByConsultant_registerParticipantAsNotificationRecipient()
    {
        $this->participant->expects($this->once())
                ->method("registerNotificationRecipient")
                ->with($this->anything(), null);
        $this->executeCreateNotificationTriggeredByConsultant(ConsultationRequest::OFFERED_BY_CONSULTANT);
    }
    public function test_createNotificationTriggeredByConsultant_addNotification()
    {
        $this->executeCreateNotificationTriggeredByConsultant(ConsultationRequest::OFFERED_BY_CONSULTANT);
        $this->assertInstanceOf(ConsultationRequestNotification::class, $this->consultationRequest->consultationRequestNotifications->first());
    }
    public function test_createNotificationTriggeredByConsultant_invalidState_doNothing()
    {
        $this->participant->expects($this->never())->method("registerMailRecipient");
        $this->executeCreateNotificationTriggeredByConsultant(ConsultationRequest::SUBMITTED_BY_PARTICIPANT);
    }
    public function test_createNotificationTriggeredByConsultant_rejectedByConsultantState_registerRecipient()
    {
        $this->participant->expects($this->once())->method("registerMailRecipient");
        $this->executeCreateNotificationTriggeredByConsultant(ConsultationRequest::REJECTED_BY_CONSULTANT);
    }
    
    protected function executeCreateNotificationTriggeredByParticipant(int $state)
    {
        $this->consultationRequest->createNotificationTriggeredByParticipant($state);
    }
    public function test_createNotificationTriggeredByParticipant_registerConsultantAsMailRecepient()
    {
        $this->consultant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationRequest, $this->anything());
        $this->executeCreateNotificationTriggeredByParticipant(ConsultationRequest::SUBMITTED_BY_PARTICIPANT);
    }
    public function test_createNotificationTriggeredByParticipant_registerConsultantAsNotificationRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerNotificationRecipient")
                ->with($this->anything());
        $this->executeCreateNotificationTriggeredByParticipant(ConsultationRequest::SUBMITTED_BY_PARTICIPANT);
    }
    public function test_createNotificationTriggeredByParticipant_addNotification()
    {
        $this->executeCreateNotificationTriggeredByParticipant(ConsultationRequest::SUBMITTED_BY_PARTICIPANT);
        $this->assertInstanceOf(ConsultationRequestNotification::class, $this->consultationRequest->consultationRequestNotifications->first());
    }
    public function test_createNotificationTriggeredByParticipant_invalidState_doNothing()
    {
        $this->participant->expects($this->never())->method("registerMailRecipient");
        $this->executeCreateNotificationTriggeredByParticipant(ConsultationRequest::REJECTED_BY_CONSULTANT);
    }
    public function test_createNotificationTriggeredByParticipant_timeChangedByParticipantState_registerRecipient()
    {
        $this->consultant->expects($this->once())->method("registerMailRecipient");
        $this->executeCreateNotificationTriggeredByParticipant(ConsultationRequest::TIME_CHANGED_BY_PARTICIPANT);
    }
    public function test_createNotificationTriggeredByParticipant_cancelledParticipantState_registerRecipient()
    {
        $this->consultant->expects($this->once())->method("registerMailRecipient");
        $this->executeCreateNotificationTriggeredByParticipant(ConsultationRequest::CANCELLED_BY_PARTICIPANT);
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
    public $consultationRequestNotifications;
    public $consultationRequestMails;

    function __construct()
    {
        parent::__construct();
    }

}
