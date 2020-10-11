<?php

namespace Notification\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\ {
    Model\Firm\Team\Member,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification,
    SharedModel\MailMessage
};
use Tests\TestBase;

class TeamTest extends TestBase
{
    protected $team;
    protected $member;
    
    protected $mailGenerator;
    protected $mailMessage;
    protected $modifiedMail;
    protected $excludedMember;
    
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->team = new TestableTeam();
        $this->team->members = new ArrayCollection();
        
        $this->member = $this->buildMockOfClass(Member::class);
        $this->team->members->add($this->member);
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->modifiedMail = $this->buildMockOfClass(MailMessage::class);
        $this->excludedMember = $this->buildMockOfClass(Member::class);
        
        $this->notification = $this->buildMockOfInterface(ContainNotification::class);
    }
    
    protected function executeRegisterAllActiveMembersAsMailRecipient()
    {
        $this->member->expects($this->any())
                ->method("isActiveMemberNotEqualsTo")
                ->willReturn(true);
        $this->mailMessage->expects($this->any())
                ->method("prependUrlPath")
                ->willReturn($this->modifiedMail);
        $this->team->registerAllActiveMembersAsMailRecipient($this->mailGenerator, $this->mailMessage, $this->excludedMember);
    }
    public function test_registerAllActiveMembersAsMailRecipient_prependUrlToMailMessage()
    {
        $this->mailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->with("/as-team-member/{$this->team->id}");
        $this->executeRegisterAllActiveMembersAsMailRecipient();
    }
    public function test_registerAllActiveMembersAsMailRecipient_executeAllActiveMemberaRegisterClientAsMailRecipient()
    {
        $this->member->expects($this->once())
                ->method("registerClientAsMailRecipient")
                ->with($this->mailGenerator, $this->modifiedMail);
        $this->executeRegisterAllActiveMembersAsMailRecipient();
    }
    public function test_registerAllActiveMembersAsMailRecipient_memberIsNotSatisfiedActiveMemberNotEqualsToCondition_skipRegisteringThisMember()
    {
        $this->member->expects($this->any())
                ->method("isActiveMemberNotEqualsTo")
                ->willReturn(false);
        $this->member->expects($this->never())
                ->method("registerClientAsMailRecipient");
        $this->executeRegisterAllActiveMembersAsMailRecipient();
    }
        
    protected function executeRegisterAllActiveMembersAsNotificationRecipient()
    {
        $this->member->expects($this->any())
                ->method("isActiveMemberNotEqualsTo")
                ->willReturn(true);
        $this->team->registerAllActiveMembersAsNotificationRecipient($this->notification, $this->excludedMember);
    }
    public function test_registerAllActiveMembersAsNotificationRecipient_executeAllMembersRegisterAsNotificationRecipientMethod()
    {
        $this->member->expects($this->once())
                ->method("registerClientAsNotificationRecipient")
                ->with($this->notification);
        $this->executeRegisterAllActiveMembersAsNotificationRecipient();
    }
    public function test_registerAllActiveMembersAsNotificationRecipient_memberNotSatisifedActiveNotEqualsToCondition_skipRegisteringThisMember()
    {
        $this->member->expects($this->once())
                ->method("isActiveMemberNotEqualsTo")
                ->willReturn(false);
        $this->member->expects($this->never())
                ->method("registerClientAsNotificationRecipient");
        $this->executeRegisterAllActiveMembersAsNotificationRecipient();
    }
}

class TestableTeam extends Team
{
    public $firm;
    public $id;
    public $name;
    public $members;
    
    function __construct()
    {
        parent::__construct();
    }
}
