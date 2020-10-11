<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\ {
    Model\Firm\Client\ClientProgramParticipation,
    Model\Firm\Program,
    Model\Firm\Team\Member,
    Model\Firm\Team\TeamProgramParticipation,
    Model\User\UserProgramParticipation,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification,
    SharedModel\MailMessage
};
use Tests\TestBase;

class ParticipantTest extends TestBase
{
    protected $participant;
    protected $program;
    protected $teamParticipant;
    protected $clientParticipant;
    protected $userParticipant;
    
    protected $mailGenerator, $mailMessage, $excludedMember;
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->participant->program = $this->program;
        
        $this->teamParticipant = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->participant->teamParticipant = $this->teamParticipant;
        
        $this->clientParticipant = $this->buildMockOfClass(ClientProgramParticipation::class);
        $this->userParticipant = $this->buildMockOfClass(UserProgramParticipation::class);
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->excludedMember = $this->buildMockOfClass(Member::class);
        
        $this->notification = $this->buildMockOfInterface(ContainNotification::class);
    }
    protected function setAsClientParticipant(): void
    {
        $this->participant->teamParticipant = null;
        $this->excludedMember = null;
        $this->participant->clientParticipant = $this->clientParticipant;
    }
    protected function setAsUserParticipant(): void
    {
        $this->participant->teamParticipant = null;
        $this->excludedMember = null;
        $this->participant->userParticipant = $this->userParticipant;
    }
    
    protected function executeGetName()
    {
        return $this->participant->getName();
    }
    public function test_getName_teamParticipant_returnTeamParticipantGetTeamNameResult()
    {
        $this->teamParticipant->expects($this->once())
                ->method("getTeamName")
                ->willReturn($teamName = "team name");
        $this->assertEquals($teamName, $this->executeGetName());
    }
    public function test_getName_clientParticipant_returnClientParticipantGetClientFullNameResult()
    {
        $this->setAsClientParticipant();
        $this->clientParticipant->expects($this->once())
                ->method("getClientFullName")
                ->willReturn($clientName = "client full name");
        $this->assertEquals($clientName, $this->executeGetName());
    }
    public function test_getName_aUserParticipant_returnUserParticipantGetUserFullNameResult()
    {
        $this->setAsUserParticipant();
        $this->userParticipant->expects($this->once())
                ->method("getUserFullName")
                ->willReturn($userName = "user name");
        $this->assertEquals($userName, $this->executeGetName());
    }
    
    public function test_getFirmDomain_returnProgramGetFirmDomainResult()
    {
        $this->program->expects($this->once())->method("getFirmDomain");
        $this->participant->getFirmDomain();
    }
    public function test_getFirmMailSenderAddress_returnProgramsGetFirmMailSenderAddressResult()
    {
        $this->program->expects($this->once())->method("getFirmMailSenderAddress");
        $this->participant->getFirmMailSenderAddress();
    }
    public function test_getFirmMailSenderName_returnProgramsGetFirmMailSenderNameResult()
    {
        $this->program->expects($this->once())->method("getFirmMailSenderName");
        $this->participant->getFirmMailSenderName();
    }
    
    protected function executeRegisterMailRecipient()
    {
        $this->participant->registerMailRecipient($this->mailGenerator, $this->mailMessage, $this->excludedMember);
    }
    public function test_registerMailRecipient_teamParticipant_registerTeamMembersAsMailRecipient()
    {
        $this->teamParticipant->expects($this->once())
                ->method("registerTeamMembersAsMailRecipient")
                ->with($this->mailGenerator, $this->mailMessage, $this->excludedMember);
        $this->executeRegisterMailRecipient();
    }
    public function test_registerMailRecipient_clientParticipant_registerClientAsMailRecipient()
    {
        $this->setAsClientParticipant();
        $this->clientParticipant->expects($this->once())
                ->method("registerClientAsMailRecipient")
                ->with($this->mailGenerator, $this->mailMessage);
        $this->executeRegisterMailRecipient();
    }
    public function test_registerMailRecipient_userParticipant_registerUserAsMailRecipient()
    {
        $this->setAsUserParticipant();
        $this->userParticipant->expects($this->once())
                ->method("registerUserAsMailRecipient")
                ->with($this->mailGenerator, $this->mailMessage);
        $this->executeRegisterMailRecipient();
    }
    
    protected function executeRegisterNotificationRecipient()
    {
        $this->participant->registerNotificationRecipient($this->notification, $this->excludedMember);
    }
    public function test_registerNotificationRecipient_aTeamParticipant_registerTeamMembersAsNotificationRecipient()
    {
        $this->teamParticipant->expects($this->once())
                ->method("registerTeamMembersAsNotificationRecipient")
                ->with($this->notification, $this->excludedMember);
        $this->executeRegisterNotificationRecipient();
    }
    public function test_registerNotificationRecipient_aClientParticipant_registerClientAsNotificationRecipient()
    {
        $this->setAsClientParticipant();
        $this->clientParticipant->expects($this->once())
                ->method("registerClientAsNotificationRecipient")
                ->with($this->notification);
        $this->executeRegisterNotificationRecipient();
    }
    public function test_registerNotificationRecipient_aUserParticipant_registerUSerAsNotificationRecipient()
    {
        $this->setAsUserParticipant();
        $this->userParticipant->expects($this->once())
                ->method("registerUserAsNotificationRecipient")
                ->with($this->notification);
        $this->executeRegisterNotificationRecipient();
    }
/*    
    public function test_qetFirmWhitelableInfo_returnProgramsGetProgramFirmWhitelableInfo()
    {
        $this->program->expects($this->once())
                ->method('getFirmWhitelableInfo');
        $this->participant->getFirmWhitelableInfo();
    }
    
    protected function executeGetName()
    {
        return $this->participant->getName();
    }
    public function test_getName_returnClientParticipantsGetClientNameResult()
    {
        $this->clientParticipant->expects($this->once())
                ->method('getClientName')
                ->willReturn($clientName = 'client name');
        $this->assertEquals($clientName, $this->participant->getName());
    }
    public function test_getName_emptyClientParticipant_returnUserParticipantGetUserNameResult()
    {
        $this->participant->clientParticipant = null;
        $this->userParticipant->expects($this->once())
                ->method('getUserName')
                ->willReturn($name = 'username');
        $this->assertEquals($name, $this->participant->getName());
    }
    
    public function test_getProgramId_returnClientParticipantsGetProgramIdResult()
    {
        $this->program->expects($this->once())
                ->method('getId');
        $this->participant->getProgramId();
    }
 * 
 */
    
}

class TestableParticipant extends Participant
{
    public $program;
    public $id;
    public $teamParticipant;
    public $clientParticipant;
    public $userParticipant;
    
    function __construct()
    {
        parent::__construct();
    }
}
