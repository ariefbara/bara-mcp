<?php

namespace Firm\Domain\Model\Firm\Program\Mission;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Mission;
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class MissionCommentTest extends TestBase
{
    protected $mission;
    protected $missionComment;
    protected $id = 'new-id', $message = 'new message', $rolePaths = ['participant' => 'participant-id'], 
            $userId = 'newuserId', $userName = 'new user name';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->mission = $this->buildMockOfClass(Mission::class);
        $missionCommentData = new MissionCommentData('message');
        $this->missionComment = new TestableMissionComment($this->mission, 'id', $missionCommentData, 'user-id', 'user name');
    }
    protected function getMissionCommentData()
    {
        $missionCommentData = new MissionCommentData($this->message);
        $missionCommentData->addRolePath(key($this->rolePaths), $this->rolePaths['participant']);
        return $missionCommentData;
    }
    
    public function test_belongsToProgram_sameProgram_returnMissionsBelongsToProgramResult()
    {
        $this->mission->expects($this->once())
                ->method('belongsToProgram')
                ->with($program = $this->buildMockOfClass(Program::class));
        $this->missionComment->belongsToProgram($program);
    }
    
    protected function executeConstruct()
    {
        return new TestableMissionComment($this->mission, $this->id, $this->getMissionCommentData(), $this->userId, $this->userName);
    }
    public function test_construct_setProperties()
    {
        $missionComment = $this->executeConstruct();
        $this->assertEquals($this->mission, $missionComment->mission);
        $this->assertEquals($this->id, $missionComment->id);
        $this->assertNull($missionComment->repliedComment);
        $this->assertEquals($this->message, $missionComment->message);
        $this->assertEquals($this->rolePaths, $missionComment->rolePaths);
        $this->assertEquals($this->userId, $missionComment->userId);
        $this->assertEquals($this->userName, $missionComment->userName);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $missionComment->modifiedTime);
    }
    
    protected function executeReceiveReply()
    {
        return $this->missionComment->receiveReply($this->id, $this->getMissionCommentData(), $this->userId, $this->userName);
    }
    public function test_receiveReply_setProperties()
    {
        $missionComment = $this->executeReceiveReply();
        $this->assertEquals($this->mission, $missionComment->mission);
        $this->assertEquals($this->id, $missionComment->id);
        $this->assertEquals($this->missionComment, $missionComment->repliedComment);
        $this->assertEquals($this->message, $missionComment->message);
        $this->assertEquals($this->rolePaths, $missionComment->rolePaths);
        $this->assertEquals($this->userId, $missionComment->userId);
        $this->assertEquals($this->userName, $missionComment->userName);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $missionComment->modifiedTime);
        
    }
}

class TestableMissionComment extends MissionComment
{
    public $mission;
    public $id;
    public $repliedComment;
    public $message;
    public $rolePaths;
    public $userId;
    public $userName;
    public $modifiedTime;
}
