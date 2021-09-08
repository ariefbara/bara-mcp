<?php

namespace Query\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Firm\Team\Member;
use Tests\TestBase;

class TeamTest extends TestBase
{
    protected $team;
    protected $member;
    
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->team = new TestableTeam();
        $this->team->members = new ArrayCollection();
        
        $this->member = $this->buildMockOfClass(Member::class);
        $this->team->members->add($this->member);
        
        $this->client = $this->buildMockOfClass(Client::class);
    }
    
    protected function hasActiveMemberCorrespondWithClient()
    {
        return $this->team->hasActiveMemberCorrespondWithClient($this->client);
    }
    public function test_hasActiveMemberCorrespondWithClient_noMemberCorrespondWithClient_returnFalse()
    {
        $this->assertFalse($this->hasActiveMemberCorrespondWithClient());
    }
    public function testhasActiveMemberCorrespondWithClient_containActiveMemberCorrespondWithClient_returnTrue()
    {
        $this->member->expects($this->any())
                ->method('isActiveMemberCorrespondWithClient')
                ->with($this->client)
                ->willReturn(true);
        $this->assertTrue($this->hasActiveMemberCorrespondWithClient());
    }
    
    protected function getListOfActiveMemberPlusTeamName()
    {
        $this->member->expects($this->any())
                ->method('isActive')
                ->willReturn(true);
        return $this->team->getListOfActiveMemberPlusTeamName();
    }
    public function test_getListOfActiveMemberPlusTeamName_returnListOfMemberClientNamePlusTeamName()
    {
        $this->member->expects($this->once())
                ->method('getClientName')
                ->willReturn($clientName = 'client name');
        $result = [
            "{$clientName} (of team: {$this->team->name})",
        ];
        $this->assertEquals($result, $this->getListOfActiveMemberPlusTeamName());
    }
    public function test_getListOfActiveMemberPlusTeamName_containInactiveMember_skipFromResult()
    {
        $this->member->expects($this->once())
                ->method('isActive')
                ->willReturn(false);
        $result = [];
        $this->assertEquals($result, $this->getListOfActiveMemberPlusTeamName());
    }
    
}

class TestableTeam extends Team
{
    public $firm;
    public $id = 'team-id';
    public $name = 'team name';
    public $creator;
    public $createdTime;
    public $members;
    
    function __construct()
    {
        parent::__construct();
    }
}
