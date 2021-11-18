<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Team\Member;
use Firm\Domain\Model\Firm\Team\MemberData;
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class TeamTest extends TestBase
{
    protected $firm;
    protected $clientOne, $clientTwo;
    protected $team;
    
    protected $id = 'newId', $name = 'new team name', $memberPosition = 'new member position';

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->clientOne = $this->buildMockOfClass(Client::class);
        $this->clientTwo = $this->buildMockOfClass(Client::class);
        
        $teamData = new TeamData('team name');
        $teamData->addMemberData(new MemberData($this->clientOne, 'position'));
        $this->team = new TestableTeam($this->firm, 'id', $teamData);
    }
    
    protected function getTeamData()
    {
        $teamData = new TeamData($this->name);
        $teamData->addMemberData(new MemberData($this->clientOne, $this->memberPosition));
        $teamData->addMemberData(new MemberData($this->clientTwo, $this->memberPosition));
        return $teamData;
    }
    
    protected function construct()
    {
        return new TestableTeam($this->firm, $this->id, $this->getTeamData());
    }
    public function test_construct_setProperties()
    {
        $team = $this->construct();
        $this->assertSame($this->firm, $team->firm);
        $this->assertSame($this->id, $team->id);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $team->createdTime);
    }
    public function test_construct_aggregateMembers()
    {
        $team = $this->construct();
        $this->assertEquals(2, $team->members->count());
        $this->assertInstanceOf(Member::class, $team->members->first());
        $this->assertInstanceOf(Member::class, $team->members->last());
    }
    public function test_construct_emptyName_badRequest()
    {
        $this->name = '';
        $this->assertRegularExceptionThrowed(function() {
            $this->construct();
        }, 'Bad Request', 'bad request: team name is mandatory');
    }
    public function test_construct_emptyMembers_forbidden()
    {
        $this->assertRegularExceptionThrowed(function() {
            return new TestableTeam($this->firm, $this->id, new TeamData($this->name));
        }, 'Forbidden', 'forbidden: team must have at least one member');
    }
    
    public function test_idEquals_sameId_returnTrue()
    {
        $this->assertTrue($this->team->idEquals($this->team->id));
    }
    public function test_idEquals_differentId_returnFalse()
    {
        $this->assertFalse($this->team->idEquals("differentId"));
    }
}

class TestableTeam extends Team
{
    public $firm;
    public $id;
    public $name;
    public $createdTime;
    public $members;
}
