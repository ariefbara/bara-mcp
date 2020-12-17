<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm\Program\Consultant;
use Notification\Domain\Model\Firm\Program\ConsultationSetup;
use Notification\Domain\Model\Firm\Program\Participant;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession\ConsultationSessionMail;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession\ConsultationSessionNotification;
use Notification\Domain\Model\Firm\Team\Member;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ConsultationSessionTest extends TestBase
{

    protected $consultationSession;
    protected $consultationSetup;
    protected $participant, $participantName = "participant name",
            $firmDomain = "firm@domain.com", $firmMailSenderAddress = "firm@domain.org", $firmMailSenderName = "firm name";
    protected $consultant, $consultantName = "consultant name";
    protected $startEndTime, $timeDescription = 'time description';
    protected $member, $memberName = "client full name";
    protected $mailMessage, $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";
    protected $subject = "Konsulta: Jadwal Konsultasi", $participantGreetings = "Hi Partisipan",
            $consultantGreetings = "Hi Konsultan", $urlPath;

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

        $this->consultationSession = new TestableConsultationSession();
        $this->consultationSession->participant = $this->participant;
        $this->consultationSession->consultant = $this->consultant;
        $this->consultationSession->startEndTime = $this->startEndTime;
        $this->consultationSession->consultationSessionMails = new ArrayCollection();
        $this->consultationSession->consultationSessionNotifications = new ArrayCollection();
        
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSession->consultationSetup = $this->consultationSetup;

        $this->urlPath = "/consultation-requests/{$this->consultationSession->id}";

        $this->member = $this->buildMockOfClass(Member::class);
        $this->member->expects($this->any())->method("getClientFullName")->willReturn($this->memberName);

        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }

    protected function executeAddAcceptNotificationTriggeredByTeamMember()
    {
        $this->consultationSession->addAcceptNotificationTriggeredByTeamMember($this->member);
    }
    public function test_addAcceptNotificationTriggeredByTeamMember_registerParticipantAsMailRecipient()
    {
        $this->participant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationSession, $this->anything(), null);
        $this->executeAddAcceptNotificationTriggeredByTeamMember();
    }
    public function test_addAcceptNotificationTriggeredByTeamMember_registerConsultantAsMailRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationSession, $this->anything());
        $this->executeAddAcceptNotificationTriggeredByTeamMember();
    }
    public function test_addAcceptNotificationTriggeredByTeamMember_registerParticipantAsNotificationRecipient()
    {
        $this->participant->expects($this->once())
                ->method("registerNotificationRecipient")
                ->with($this->anything(), $this->member);
        $this->executeAddAcceptNotificationTriggeredByTeamMember();
    }
    public function test_addAcceptNotificationTriggeredByTeamMember_registerConsultantAsNotificationRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerNotificationRecipient");
        $this->executeAddAcceptNotificationTriggeredByTeamMember();
    }
    public function test_addAcceptNotificationTriggeredByTeamMember_registerAllCoordinatorsAsMailRecipient()
    {
        $this->consultationSetup->expects($this->once())
                ->method("registerAllCoordinatorsAsMailRecipient")
                ->with($this->consultationSession);
        $this->executeAddAcceptNotificationTriggeredByTeamMember();
    }
    public function test_addAcceptNotificationTriggeredByTeamMember_registerAllCoordinatorNotificationRecipient()
    {
        $this->consultationSetup->expects($this->once())
                ->method("registerAllCoordinatorsAsNotificationRecipient");
        $this->executeAddAcceptNotificationTriggeredByTeamMember();
    }
    public function test_addAcceptNotificationTriggeredByTeamMember_addNotification()
    {
        $this->executeAddAcceptNotificationTriggeredByTeamMember();
        $this->assertInstanceOf(ConsultationSessionNotification::class, $this->consultationSession->consultationSessionNotifications->first());
    }
    
    protected function executeAddAcceptNotificationTriggeredByParticipant()
    {
        $this->consultationSession->addAcceptNotificationTriggeredByParticipant();
    }
    public function test_addAcceptNotificationTriggeredByParticipant_registerConsultantAsMailRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationSession, $this->anything());
        $this->executeAddAcceptNotificationTriggeredByParticipant();
    }
    public function test_addAcceptNotificationTriggeredByParticipant_registerParticipantAsMailRecipient()
    {
        $this->participant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationSession, $this->anything());
        $this->executeAddAcceptNotificationTriggeredByParticipant();
    }
    public function test_addAcceptNotificationTriggeredByParticipant_registerConsultantAsNotificationRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerNotificationRecipient");
        $this->executeAddAcceptNotificationTriggeredByParticipant();
    }
    public function test_addAcceptNotificationTriggeredByParticipant_addNotification()
    {
        $this->executeAddAcceptNotificationTriggeredByParticipant();
        $this->assertInstanceOf(ConsultationSessionNotification::class, $this->consultationSession->consultationSessionNotifications->first());
    }
    public function test_addAcceptNotificationTriggeredByParticipant_registerAllCoordinatorsAsMailRecipientAndNotificationRecipient()
    {
        $this->consultationSetup->expects($this->once())
                ->method("registerAllCoordinatorsAsMailRecipient")
                ->with($this->consultationSession);
        $this->consultationSetup->expects($this->once())
                ->method("registerAllCoordinatorsAsNotificationRecipient");
        $this->executeAddAcceptNotificationTriggeredByParticipant();
    }
    
    protected function executeAddAcceptNotificationTriggeredByConsultant()
    {
        $this->consultationSession->addAcceptNotificationTriggeredByConsultant();
    }
    public function test_addAcceptNotificationTriggeredByConsultant_registerParticipantAsMailRecipient()
    {
        $this->participant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationSession, $this->anything());
        $this->consultationSession->addAcceptNotificationTriggeredByConsultant();
    }
    public function test_addAcceptNotificationTriggeredByConsultant_registerConsultantAsMailRecipient()
    {
        $this->consultant->expects($this->once())
                ->method("registerMailRecipient")
                ->with($this->consultationSession, $this->anything());
        $this->consultationSession->addAcceptNotificationTriggeredByConsultant();
    }
    public function test_addAcceptNotificationTriggeredByConsultant_registerParticipantAsNotificationRecipient()
    {
        $this->participant->expects($this->once())
                ->method("registerNotificationRecipient");
        
        $this->consultationSession->addAcceptNotificationTriggeredByConsultant();
    }
    public function test_addAcceptNotificationTriggeredByConsultant_addNotification()
    {
        $this->consultationSession->addAcceptNotificationTriggeredByConsultant();
        $this->assertInstanceOf(ConsultationSessionNotification::class, $this->consultationSession->consultationSessionNotifications->first());
    }
    public function test_addAcceptNotificationTriggeredByConsultant_registerAllCoordinatorsAsMailRecipientAndNotificationRecipient()
    {
        $this->consultationSetup->expects($this->once())
                ->method("registerAllCoordinatorsAsMailRecipient")
                ->with($this->consultationSession);
        $this->consultationSetup->expects($this->once())
                ->method("registerAllCoordinatorsAsNotificationRecipient");
        $this->executeAddAcceptNotificationTriggeredByConsultant();
    }
    
    public function test_addMail_addConsultationRequestMailNotCollection()
    {
        $this->participant->expects($this->once())->method("getFirmMailSenderAddress");
        $this->participant->expects($this->once())->method("getFirmMailSenderName");
        $this->consultationSession->addMail($this->mailMessage, $this->recipientMailAddress, $this->recipientName);
        $this->assertInstanceOf(ConsultationSessionMail::class, $this->consultationSession->consultationSessionMails->first());
    }

}

class TestableConsultationSession extends ConsultationSession
{

    public $participant;
    public $id;
    public $consultant;
    public $consultationSetup;
    public $startEndTime;
    public $consultationSessionMails;
    public $consultationSessionNotifications;
    
    function __construct()
    {
        parent::__construct();
    }

}
