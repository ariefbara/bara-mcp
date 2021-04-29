<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\User;
use Tests\TestBase;

class UserParticipantTest extends TestBase
{
    protected $userParticipant;
    protected $participant;
    protected $user;
    protected $id = 'id';
    protected $registrant;
    protected $meetingId = "meetingId", $meetingType, $meetingData;
    
    protected $mission, $missionComment, $missionCommentId = 'missionCommentId', $missionCommentData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->user = $this->buildMockOfClass(User::class);
        $this->userParticipant = new TestableUserParticipant($this->participant, 'id', $this->user);
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionComment = $this->buildMockOfClass(MissionComment::class);
        $this->missionCommentData = $this->buildMockOfClass(MissionCommentData::class);
    }
    
    public function test_construct_setProperties()
    {
        $userParticipant = new TestableUserParticipant($this->participant, $this->id, $this->user);
        $this->assertEquals($this->participant, $userParticipant->participant);
        $this->assertEquals($this->id, $userParticipant->id);
        $this->assertEquals($this->user, $userParticipant->user);
    }
    
    public function test_correspondWithRegistrant_returnRegistrantsCorrespondWithUserResult()
    {
        $this->registrant->expects($this->once())
                ->method('correspondWithUser')
                ->with($this->userParticipant->user);
        $this->userParticipant->correspondWithRegistrant($this->registrant);
    }
    
    public function test_initiateMeeting_returnParticipantInitiateMeetingResult()
    {
        $this->participant->expects($this->once())
                ->method("initiateMeeting")
                ->with($this->meetingId, $this->meetingType, $this->meetingData);
        $this->userParticipant->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
    
    protected function executeSubmitCommentInMission()
    {
        $this->userParticipant->submitCommentInMission($this->mission, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_submitCommentInMission_returnUsersSubmitCommentInMissionResult()
    {
        $this->user->expects($this->once())
                ->method('submitCommentInMission')
                ->with($this->mission, $this->missionCommentId, $this->missionCommentData);
        $this->executeSubmitCommentInMission();
    }
    public function test_submitCommentInMission_modifyCommentDataRolePaths()
    {
        $this->missionCommentData->expects($this->once())
                ->method('addRolePath')
                ->with('participant', $this->userParticipant->id);
        $this->executeSubmitCommentInMission();
    }
    public function test_submitCommentInMission_assertActiveParticipant()
    {
        $this->participant->expects($this->once())
                ->method('assertActive');
        $this->executeSubmitCommentInMission();
    }
    public function test_submitCommentInMission_assertMissionAccessibleByParticipant()
    {
        $this->participant->expects($this->once())
                ->method('assertAssetAccessible')
                ->with($this->mission);
        $this->executeSubmitCommentInMission();
    }
    
    protected function executeReplyMissionComment()
    {
        $this->userParticipant->replyMissionComment($this->missionComment, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_replyMissionComment_returnUsersReplyMissionCommentResult()
    {
        $this->user->expects($this->once())
                ->method('replyMissionComment')
                ->with($this->missionComment, $this->missionCommentId, $this->missionCommentData);
        $this->executeReplyMissionComment();
    }
    public function test_replyMissionComment_modifyCommentDataRolePaths()
    {
        $this->missionCommentData->expects($this->once())
                ->method('addRolePath')
                ->with('participant', $this->userParticipant->id);
        $this->executeReplyMissionComment();
    }
    public function test_replyMissionComment_assertActiveParticipant()
    {
        $this->participant->expects($this->once())
                ->method('assertActive');
        $this->executeReplyMissionComment();
    }
    public function test_replyMissionComment_assertMissionAccessibleByParticipant()
    {
        $this->participant->expects($this->once())
                ->method('assertAssetAccessible')
                ->with($this->missionComment);
        $this->executeReplyMissionComment();
    }
    
}

class TestableUserParticipant extends UserParticipant
{
    public $participant;
    public $id;
    public $user;
}
