<?php

namespace Team\Domain\Model\Team;

use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Team\Domain\DependencyModel\Firm\Client;
use Team\Domain\Model\Team;
use Tests\TestBase;

class MemberTest extends TestBase
{
    protected $team;
    protected $client;
    
    protected $member;
    
    protected $id = "newMemberId", $anAdmin = false, $position = "new member position";
    
    protected $otherMember;
    
    protected $teamFileInfoId = "teamFileInfoId", $fileInfoData;
    //
    protected $teamTask, $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->team = $this->buildMockOfClass(Team::class);
        $this->client = $this->buildMockOfClass(\Team\Domain\DependencyModel\Firm\Client::class);
        
        $this->member = new TestableMember($this->team, "id", $this->client, true, "position");
        
        $this->otherMember = $this->buildMockOfClass(Member::class);
        
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())->method("getName")->willReturn("fileName.txt");
        //
        $this->teamTask = $this->buildMockOfInterface(\Team\Domain\Task\TeamTask::class);
    }
    public function test_construct_setProperties()
    {
        $member = new TestableMember($this->team, $this->id, $this->client, $this->anAdmin, $this->position);
        $this->assertEquals($this->team, $member->team);
        $this->assertEquals($this->id, $member->id);
        $this->assertEquals($this->client, $member->client);
        $this->assertEquals($this->anAdmin, $member->anAdmin);
        $this->assertEquals($this->position, $member->position);
        $this->assertTrue($member->active);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $member->joinTime);
    }
    
    protected function executeAddTeamMember()
    {
        return $this->member->addTeamMember($this->client, $this->anAdmin, $this->position);
    }
    public function test_addTeamMember_returnTeamsAddMemberResult()
    {
        $this->team->expects($this->once())
                ->method("addMember")
                ->with($this->client, $this->anAdmin, $this->position)
                ->willReturn($memberId = 'memberId');
        $this->assertEquals($memberId, $this->executeAddTeamMember());
    }
    public function test_addTeamMember_notAdmin_forbiddenError()
    {
        $this->member->anAdmin = false;
        $operation = function (){
            $this->executeAddTeamMember();
        };
        $errorDetail = "forbidden: only team member with admin priviledge can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_addTeamMember_inactiveMember_forbiddenError()
    {
        $this->member->active = false;
        $operation = function (){
            $this->executeAddTeamMember();
        };
        $errorDetail = "forbidden: only active team member can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeRemoveOtherMember()
    {
        $this->member->removeOtherMember($this->otherMember);
    }
    public function test_removeOtherMember_removeOtherMember()
    {
        $this->otherMember->expects($this->once())
                ->method("remove");
        $this->executeRemoveOtherMember();
    }
    public function test_removeOtherMember_notAdmin_forbiddenError()
    {
        $this->member->anAdmin = false;
        $operation = function (){
            $this->executeRemoveOtherMember();
        };
        $errorDetail = "forbidden: only team member with admin priviledge can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_removeOtherMember_inactiveMember_forbiddenError()
    {
        $this->member->active = false;
        $operation = function (){
            $this->executeRemoveOtherMember();
        };
        $errorDetail = "forbidden: only active team member can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeRemove()
    {
        $this->member->remove();
    }
    public function test_remove_setActiveFalse()
    {
        $this->executeRemove();
        $this->assertFalse($this->member->active);
    }
    public function test_remove_alreadyInactive_forbiddenError()
    {
        $this->member->active = false;
        $operation = function (){
            $this->executeRemove();
        };
        $errorDetail = "forbidden: member already inactive";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeActivate()
    {
        $this->member->activate($this->anAdmin, $this->position);
    }
    public function test_activate_setActiveStatus_adminStatus_andPosition()
    {
        $this->member->active = false;
        $this->executeActivate();
        $this->assertTrue($this->member->active);
        $this->assertEquals($this->anAdmin, $this->member->anAdmin);
        $this->assertEquals($this->position, $this->member->position);
    }
    public function test_activate_alreadyActiveMember_forbiddenError()
    {
        $operation = function (){
            $this->executeActivate();
        };
        $errorDetail = "forbidden: member already active";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_isCorrespondWithClient_sameClient_returnTrue()
    {
        $this->assertTrue($this->member->isCorrespondWithClient($this->client));
    }
    public function test_isCorrespondWithClient_differentClient_returnFalse()
    {
        $this->assertFalse($this->member->isCorrespondWithClient($this->buildMockOfClass(Client::class)));
    }
    
    protected function executeUploadFile()
    {
        return $this->member->uploadFile($this->teamFileInfoId, $this->fileInfoData);
    }
    public function test_uploadFile_returnTeamFileInfo()
    {
        $teamFileInfo = new TeamFileInfo($this->team, $this->teamFileInfoId, $this->fileInfoData);
        $this->assertEquals($teamFileInfo, $this->executeUploadFile());
    }
    public function test_uploadFile_inactiveMember_forbiddenError()
    {
        $this->member->active = false;
        $operation = function (){
            $this->executeUploadFile();
        };
        $errorDetail = "forbidden: only active team member can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    //
    protected function executeTeamTask()
    {
        $this->member->executeTeamTask($this->teamTask, $this->payload);
    }
    public function test_executeTeamTask_executeTask()
    {
        $this->teamTask->expects($this->once())
                ->method('execute')
                ->with($this->team, $this->payload);
        $this->executeTeamTask();
    }
    public function test_executeTeamTask_inactiveMember_forbidden()
    {
        $this->member->active = false;
        $this->assertRegularExceptionThrowed(function () {
            $this->executeTeamTask();
        }, 'Forbidden', 'only active team member can make this request');
    }
}

class TestableMember extends Member
{
    public $team;
    public $id;
    public $client;
    public $anAdmin;
    public $position;
    public $active;
    public $joinTime;
}
