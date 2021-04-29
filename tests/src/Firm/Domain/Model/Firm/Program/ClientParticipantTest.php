<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Tests\TestBase;

class ClientParticipantTest extends TestBase
{
    protected $participant;
    protected $client;
    protected $clientParticipant;
    protected $id = 'newClientParticipantId';
    protected $registrant;
    protected $meetingId = "meetingId", $meetingType, $meetingData;
    
    protected $mission, $missionComment, $missionCommentId = 'missionCommentId', $missionCommentData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->client = $this->buildMockOfClass(Client::class);
        
        $this->clientParticipant = new TestableClientParticipant($this->participant, 'id', $this->client);
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionComment = $this->buildMockOfClass(MissionComment::class);
        $this->missionCommentData = $this->buildMockOfClass(MissionCommentData::class);
    }
    
    public function test_construct_setProperties()
    {
        $clientParticipant = new TestableClientParticipant($this->participant, $this->id, $this->client);
        $this->assertEquals($this->participant, $clientParticipant->participant);
        $this->assertEquals($this->id, $clientParticipant->id);
        $this->assertEquals($this->client, $clientParticipant->client);
    }
    
    public function test_correspondWithRegistrant_returnRegistrantsCorrespondWithClientResult()
    {
        $this->registrant->expects($this->once())
                ->method('correspondWithClient')
                ->with($this->client);
        
        $this->clientParticipant->correspondWithRegistrant($this->registrant);
    }
    
    public function test_initiateMeeting_returnParticipantInitiateMeetingResult()
    {
        $this->participant->expects($this->once())
                ->method("initiateMeeting")
                ->with($this->meetingId, $this->meetingType, $this->meetingData);
        $this->clientParticipant->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
    
    protected function executeSubmitCommentInMission()
    {
        $this->clientParticipant->submitCommentInMission($this->mission, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_submitCommentInMission_returnClientsSubmitCommentInMissionResult()
    {
        $this->client->expects($this->once())
                ->method('submitCommentInMission')
                ->with($this->mission, $this->missionCommentId, $this->missionCommentData);
        $this->executeSubmitCommentInMission();
    }
    public function test_submitCommentInMission_modifyCommentDataRolePaths()
    {
        $this->missionCommentData->expects($this->once())
                ->method('addRolePath')
                ->with('participant', $this->clientParticipant->id);
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
        $this->clientParticipant->replyMissionComment($this->missionComment, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_replyMissionComment_returnClientsReplyMissionCommentResult()
    {
        $this->client->expects($this->once())
                ->method('replyMissionComment')
                ->with($this->missionComment, $this->missionCommentId, $this->missionCommentData);
        $this->executeReplyMissionComment();
    }
    public function test_replyMissionComment_modifyCommentDataRolePaths()
    {
        $this->missionCommentData->expects($this->once())
                ->method('addRolePath')
                ->with('participant', $this->clientParticipant->id);
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

class TestableClientParticipant extends ClientParticipant
{
    public $participant;
    public $id = 'client-participant-id';
    public $client;
}
