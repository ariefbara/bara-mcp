<?php

namespace Notification\Domain\Model\Firm\Team;

use Notification\Domain\ {
    Model\Firm\Client,
    Model\Firm\Team,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification,
    SharedModel\MailMessage
};
use Tests\TestBase;

class MemberTest extends TestBase
{
    protected $member;
    protected $client;
    protected $team, $teamId = "teamId";
    
    protected $mailGenerator;
    protected $mailMessage;
    protected $notification;
    protected $excludedMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->member = new TestableMember();
        
        $this->client = $this->buildMockOfClass(Client::class);
        $this->member->client = $this->client;
        
        $this->team = $this->buildMockOfClass(Team::class);
        $this->team->expects($this->any())->method("getId")->willReturn($this->teamId);
        $this->member->team = $this->team;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->notification = $this->buildMockOfInterface(ContainNotification::class);
        
        $this->excludedMember = new TestableMember();
        $this->excludedMember->id = "differentId";
    }
    
    public function test_getClientFullName_returnClientGetFullNameResult()
    {
        $this->client->expects($this->once())
                ->method("getFullName");
        $this->member->getClientFullName();
    }
    
    public function test_registerClientAsMailRecipient_mofidyMessageUrlPath()
    {
        $this->mailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->with("/as-team-member/{$this->teamId}");
        $this->member->registerClientAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    public function test_registerClientAsMailRecipient_executeClientRegisterAsMailRecipient()
    {
        $this->mailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->willReturn($mailMessage = $this->buildMockOfClass(MailMessage::class));
        $this->client->expects($this->once())
                ->method("registerAsMailRecipient")
                ->with($this->mailGenerator, $mailMessage);
        $this->member->registerClientAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    
    public function test_registerClientAsNotificationRecipient_addClientRecipientToNotification()
    {
        $this->notification->expects($this->once())
                ->method("addClientRecipient")
                ->with($this->client);
        $this->member->registerClientAsNotificationRecipient($this->notification);
    }
    
    protected function executeIsActiveMemberNotEqualsTo()
    {
        return $this->member->isActiveMemberNotEqualsTo($this->excludedMember);
    }
    public function test_isActiveMemberNotEqualsTo_returnTrue()
    {
        $this->assertTrue($this->executeIsActiveMemberNotEqualsTo());
    }
    public function test_isActiveMemberNotEqualsTo_inactiveMember_returnFalse()
    {
        $this->member->active = false;
        $this->assertFalse($this->executeIsActiveMemberNotEqualsTo());
    }
    public function test_isActiveMemberNotEqualsTo_sameIdAsExcludedMember_returnFalse()
    {
        $this->excludedMember->id = $this->member->id;
        $this->assertFalse($this->executeIsActiveMemberNotEqualsTo());
    }
    public function test_isActiveMemberNotEqualsTo_emptyExcludedMember_returnActiveStatus()
    {
        $this->excludedMember = null;
        $this->assertEquals($this->member->active, $this->executeIsActiveMemberNotEqualsTo());
    }
}

class TestableMember extends Member
{
    public $team;
    public $id = "memberId";
    public $client;
    public $active = true;
    
    function __construct()
    {
        parent::__construct();
    }
}
