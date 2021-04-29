<?php

namespace Firm\Domain\Model;

use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Resources\Domain\ValueObject\PersonName;
use Tests\TestBase;

class UserTest extends TestBase
{

    protected $user;
    protected $personName, $fullName = 'user name';
    protected $mission, $missionComment, $missionCommentId = 'missionCommentId', $missionCommentData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new TestableUser();
        $this->personName = $this->buildMockOfClass(PersonName::class);
        $this->personName->expects($this->any())->method('getFullName')->willReturn($this->fullName);
        $this->user->personName = $this->personName;

        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionComment = $this->buildMockOfClass(MissionComment::class);
        $this->missionCommentData = $this->buildMockOfClass(MissionCommentData::class);
    }
    
    protected function executeSubmitCommentInMission()
    {
        $this->user->submitCommentInMission($this->mission, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_submitCommentInMission_returnMissionsReceiveCommentResult()
    {
        $this->mission->expects($this->once())
                ->method('receiveComment')
                ->with($this->missionCommentId, $this->missionCommentData, $this->user->id, $this->fullName);
        $this->executeSubmitCommentInMission();
    }
    
    protected function executeReplyMissionComment()
    {
        $this->user->replyMissionComment($this->missionComment, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_replyMissionComment_returnMissionCommentssReceiveReplyResult()
    {
        $this->missionComment->expects($this->once())
                ->method('receiveReply')
                ->with($this->missionCommentId, $this->missionCommentData, $this->user->id, $this->fullName);
        $this->executeReplyMissionComment();
    }

}

class TestableUser extends User
{

    public $id = 'user-id';
    public $personName;

    function __construct()
    {
        parent::__construct();
    }

}
