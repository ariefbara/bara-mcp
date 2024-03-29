<?php

namespace Firm\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\Registrant;
use Firm\Domain\Model\Firm\Program\TeamParticipant;
use Firm\Domain\Model\Firm\Team\Member;
use Firm\Domain\Model\Firm\Team\MemberData;
use Firm\Domain\Model\Firm\Team\TeamRegistrant;
use Query\Domain\Model\Firm\ParticipantTypes;
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\ValueObject\CustomerInfo;
use Tests\TestBase;

class TeamTest extends TestBase
{
    protected $firm;
    protected $clientOne, $clientTwo;
    protected $team, $member, $teamParticipant, $teamRegistrant;
    
    protected $id = 'newId', $name = 'new team name', $memberPosition = 'new member position';
    protected $memberId = 'memberId';
    protected $program;
    //
    protected $participantId = 'participantId', $participant;
    protected $registrantId = 'registrantId', $registrant;
    //
    protected $participantTypes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->clientOne = $this->buildMockOfClass(Client::class);
        $this->clientTwo = $this->buildMockOfClass(Client::class);
        
        $teamData = new TeamData('team name');
        $teamData->addMemberData(new MemberData($this->clientOne, 'position'));
        $this->team = new TestableTeam($this->firm, 'id', $teamData);
        
        $this->team->teamParticipants = new ArrayCollection();
        $this->teamParticipant = $this->buildMockOfClass(TeamParticipant::class);
        $this->team->teamParticipants->add($this->teamParticipant);
        
        $this->teamRegistrant = $this->buildMockOfClass(TeamRegistrant::class);
        $this->team->teamRegistrants = new ArrayCollection();
        $this->team->teamRegistrants->add($this->teamRegistrant);
        
        $this->member = $this->buildMockOfClass(Member::class);
        $this->team->members->clear();
        $this->team->members->add($this->member);
        $this->program = $this->buildMockOfClass(Program::class);
        //
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        //
        $this->participantTypes = $this->buildMockOfClass(ParticipantTypes::class);
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
    
    protected function assertManageableInFirm()
    {
        $this->team->assertManageableInFirm($this->firm);
    }
    public function test_assertManageableInFirm_sameFirm_void()
    {
        $this->assertManageableInFirm();
        $this->markAsSuccess();
    }
    public function test_assertManageableInFirm_differentFirm_forbidden()
    {
        $this->team->firm = $this->buildMockOfClass(Firm::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertManageableInFirm();
        }, 'Forbidden', 'forbidden: can only manage team in same firm');
    }
    
    public function test_idEquals_sameId_returnTrue()
    {
        $this->assertTrue($this->team->idEquals($this->team->id));
    }
    public function test_idEquals_differentId_returnFalse()
    {
        $this->assertFalse($this->team->idEquals("differentId"));
    }
    
    protected function addMember()
    {
        $memberData = new MemberData($this->clientOne, $this->memberPosition);
        return $this->team->addMember($memberData);
    }
    public function test_addMember_addClientAsNewMember()
    {
        $this->addMember();
        $this->assertEquals(2, $this->team->members->count());
        $this->assertInstanceOf(Member::class, $this->team->members->last());
    }
    public function test_addMember_alreadyContainMemberCorrespondWithSameClient_enableCorrepondingMember()
    {
        $this->member->expects($this->once())
                ->method('correspondWithClient')
                ->with($this->clientOne)
                ->willReturn(true);
        $this->member->expects($this->once())
                ->method('enable')
                ->with($this->memberPosition);
        $this->addMember();
    }
    public function test_addMember_alreadyContainMemberCorrespondWithSameClient_preventAddingAsNewMember()
    {
        $this->member->expects($this->once())
                ->method('correspondWithClient')
                ->with($this->clientOne)
                ->willReturn(true);
        $this->addMember();
        $this->assertEquals(1, $this->team->members->count());
    }
    public function test_addMember_returnMemberId()
    {
        $this->member->expects($this->once())
                ->method('correspondWithClient')
                ->with($this->clientOne)
                ->willReturn(true);
        $this->member->expects($this->once())
                ->method('getId')
                ->willReturn($this->memberId);
        $this->assertEquals($this->memberId, $this->addMember());
    }
    
    protected function disableMember()
    {
        $this->member->expects($this->any())
                ->method('getId')
                ->willReturn($this->memberId);
        $this->team->disableMember($this->memberId);
    }
    public function test_disableMember_disableCorrespondMember()
    {
        $this->member->expects($this->once())
                ->method('disable');
        $this->disableMember();
    }
    public function test_disableMember_noCorrepondingMemberFound_notFound()
    {
        $this->member->expects($this->any())
                ->method('getId')
                ->willReturn('differentMember');
        $this->assertRegularExceptionThrowed(function() {
            $this->disableMember();
        }, 'Not Found', 'not found: team member not found');
    }
    
    protected function addToProgram()
    {
        return $this->team->addToProgram($this->program);
    }
    public function test_addToProgram_addTeamParticipantToCollection()
    {
        $this->addToProgram();
        $this->assertEquals(2, $this->team->teamParticipants->count());
        $this->assertInstanceOf(TeamParticipant::class, $this->team->teamParticipants->last());
    }
    public function test_addToProgram_formerParticipantOfSameProgram_enablePreviousParticipation()
    {
        $this->teamParticipant->expects($this->once())
                ->method('correspondWithProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->teamParticipant->expects($this->once())
                ->method('enable');
        $this->addToProgram();
    }
    public function test_addToProgram_formerParticipantOfSameProgram_preventAddNewParticipant()
    {
        $this->teamParticipant->expects($this->once())
                ->method('correspondWithProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->addToProgram();
        $this->assertEquals(1, $this->team->teamParticipants->count());
    }
    public function test_addToProgram_returnTeamParticipantId()
    {
        $this->teamParticipant->expects($this->once())
                ->method('correspondWithProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->teamParticipant->expects($this->once())
                ->method('getId')
                ->willReturn($teamParticipantId = 'teamParticipantId');
        $this->assertEquals($teamParticipantId, $this->addToProgram());
    }
    public function test_addToProgram_assertProgramCanAcceptTeamTypeParticipant()
    {
        $this->program->expects($this->once())
                ->method('assertCanAcceptParticipantOfType')
                ->with('team');
        $this->addToProgram();
    }
    
    protected function assertUsableInFirm()
    {
        $this->team->assertUsableInFirm($this->firm);
    }
    public function test_assertUsableInFirm_sameFirm_void()
    {
        $this->assertUsableInFirm();
        $this->markAsSuccess();
    }
    public function test_assertUsableInFirm_differentFirm_forbidden()
    {
        $this->team->firm = $this->buildMockOfClass(Firm::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertUsableInFirm();
        }, 'Forbidden', 'forbidden: unable to use team from different firm');
    }
    
    //
    protected function addProgramParticipation()
    {
        $this->team->addProgramParticipation($this->participantId, $this->participant);
    }
    public function test_addProgramParticipation_addTeamParticipantToCollection()
    {
        $this->addProgramParticipation();
        $this->assertEquals(2, $this->team->teamParticipants->count());
        $this->assertInstanceOf(TeamParticipant::class, $this->team->teamParticipants->last());
    }
    
    //
    protected function addProgramRegistration()
    {
        $this->team->addProgramRegistration($this->registrantId, $this->registrant);
    }
    public function test_addProgramRegistration_addTeamRegistrantToCollection()
    {
        $this->addProgramRegistration();
        $this->assertEquals(2, $this->team->teamRegistrants->count());
        $this->assertInstanceOf(TeamRegistrant::class, $this->team->teamRegistrants->last());
    }
    
    //
    protected function assertBelongsInFirm()
    {
        $this->team->assertBelongsInFirm($this->firm);
    }
    public function test_assertBelongsInFirm_differentFirm_forbidden()
    {
        $this->team->firm = $this->buildMockOfClass(Team::class);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertBelongsInFirm();
        }, 'Forbidden', 'team not belongs in same firm');
    }
    public function test_assertBelongsInFirm_sameFirm_void()
    {
        $this->assertBelongsInFirm();
        $this->markAsSuccess();
    }
    
    //
    protected function getUserType()
    {
        return $this->team->getUserType();
    }
    public function test_getUserType_returnTeamType()
    {
        $this->assertSame(ParticipantTypes::TEAM_TYPE, $this->getUserType());
    }
    
    //
    protected function assertNoActiveParticipationOrOngoingRegistrationInProgram()
    {
        $this->team->assertNoActiveParticipationOrOngoingRegistrationInProgram($this->program);
    }
    public function test_assertNoActiveParticipationOrOngoingRegistrationInProgram_hasUnconcludedRegistrationInSameProgram_forbidden()
    {
        $this->teamRegistrant->expects($this->any())
                ->method('isUnconcludedRegistrationInProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertNoActiveParticipationOrOngoingRegistrationInProgram();
        }, 'Forbidden', 'program application refused, team has unconcluded registration in same program');
    }
    public function test_assertNoActiveParticipationOrOngoingRegistrationInProgram_noUnconcludedRegistrationInSameProgram_void()
    {
        $this->assertNoActiveParticipationOrOngoingRegistrationInProgram();
        $this->markAsSuccess();
    }
    public function test_assertNoActiveParticipationOrOngoingRegistrationInProgram_hasActiveParticipationInSameProgram_forbidden()
    {
        $this->teamParticipant->expects($this->any())
                ->method('isActiveParticipantInProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertNoActiveParticipationOrOngoingRegistrationInProgram();
        }, 'Forbidden', 'program application refused, team is active participant in same program');
    }
    public function test_assertNoActiveParticipationOrOngoingRegistrationInProgram_noActiveRegistrationInSameProgram_void()
    {
        $this->assertNoActiveParticipationOrOngoingRegistrationInProgram();
        $this->markAsSuccess();
    }
    
    //
    protected function assertTypeIncludedIn()
    {
        $this->participantTypes->expects($this->any())
                ->method('hasType')
                ->with(ParticipantTypes::TEAM_TYPE)
                ->willReturn(true);
        $this->team->assertTypeIncludedIn($this->participantTypes);
    }
    public function test_assertTypeInclucedIn_teamTypeIsNotIncludedInParticipantType_forbidden()
    {
        $this->participantTypes->expects($this->once())
                ->method('hasType')
                ->with(ParticipantTypes::TEAM_TYPE)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function (){
            $this->assertTypeIncludedIn();
        }, 'Forbidden', 'program application refused, team type is not accomodate in program');
    }
    public function test_assertTypeInclucedIn_teamTypeIsIncludedInParticipantType_void()
    {
        $this->assertTypeIncludedIn();
        $this->markAsSuccess();
    }
    
    //
    protected function getCustomerInfo()
    {
        return $this->team->getCustomerInfo();
    }
    public function test_getCustomerInfo_returnCustomerInfo()
    {
        $customerInfo = new CustomerInfo($this->team->name, 'donotsend@innov.id');
        $this->assertEquals($customerInfo, $this->getCustomerInfo());
    }
}

class TestableTeam extends Team
{
    public $firm;
    public $id;
    public $name;
    public $createdTime;
    public $members;
    public $teamRegistrants;
    public $teamParticipants;
}
