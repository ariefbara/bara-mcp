<?php

namespace Team\Domain\Model;

use Resources\DateTimeImmutableBuilder;
use Team\Domain\DependencyModel\Firm\Client;
use Team\Domain\Event\TeamHasAppliedToProgram;
use Team\Domain\Model\Team\Member;
use Tests\TestBase;

class TeamTest extends TestBase
{
    protected $client;
    protected $team;
    
    protected $firmId = "firmId", $id = "newTeamId", $name = "new team name", $memberPosition = "new member position";
    
    protected $member;
    protected $anAdmin = true;
    //
    protected $programId = 'programId';
            
    function setUp(): void {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->team = new TestableTeam("firmId", "id", $this->client, "team name", "member position");
        
        $this->team->members->clear();
        $this->member = $this->buildMockOfClass(Member::class);
        $this->team->members->add($this->member);
    }
    private function executeConstruct() {
        return new TestableTeam($this->firmId, $this->id, $this->client, $this->name, $this->memberPosition);
    }
    function test_construct_setProperties() {
        $team = $this->executeConstruct();
        $this->assertEquals($this->firmId, $team->firmId);
        $this->assertEquals($this->id, $team->id);
        $this->assertEquals($this->name, $team->name);
        $this->assertEquals($this->client, $team->creator);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $team->createdTime);
    }
    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: team name is required';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    function test_construct_addCreatorAsActiveTeamMembership() {
        $team = $this->executeConstruct();
        $this->assertEquals(1, count($team->members));
    }
    
    protected function executeAddMember()
    {
        return $this->team->addMember($this->client, $this->anAdmin, $this->memberPosition);
    }
    public function test_addMember_addMemberToCollection()
    {
        $this->executeAddMember();
        $this->assertEquals(2, $this->team->members->count());
        $this->assertInstanceOf(Member::class, $this->team->members->last());
    }
    public function test_addMember_alreadyHasMemberCorrepondWithClient_activateThisMember()
    {
        $this->member->expects($this->once())
                ->method("isCorrespondWithClient")
                ->with($this->client)
                ->willReturn(true);
        $this->member->expects($this->once())
                ->method("activate")
                ->with($this->anAdmin, $this->memberPosition);
        $this->executeAddMember();
    }
    public function test_addMember_alreadyHasMemberCorrespondWithClient_preventAddNewMember()
    {
        $this->member->expects($this->once())
                ->method("isCorrespondWithClient")
                ->with($this->client)
                ->willReturn(true);
        $this->executeAddMember();
        $this->assertEquals(1, $this->team->members->count());
    }
    public function test_addMember_returnMemberId()
    {
        $this->member->expects($this->once())
                ->method("isCorrespondWithClient")
                ->with($this->client)
                ->willReturn(true);
        $this->member->expects($this->once())
                ->method("getId")
                ->willReturn($memberId = "memberId");
        $this->assertEquals($memberId, $this->executeAddMember());
    }
    
    //
    protected function applyToProgram()
    {
        $this->team->applyToProgram($this->programId);
    }
    public function test_applyToProgram_recordEvent()
    {
        $this->applyToProgram();
        $event = new TeamHasAppliedToProgram($this->team->firmId, $this->team->id, $this->programId);
        $this->assertEquals($event, $this->team->recordedEvents[0]);
    }
    
}

class TestableTeam extends Team{
    public $firmId;
    public $id;
    public $name;
    public $creator;
    public $createdTime;
    public $members;
    //
    public $recordedEvents;
}

