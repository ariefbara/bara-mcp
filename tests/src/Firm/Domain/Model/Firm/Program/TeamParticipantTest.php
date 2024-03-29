<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Firm\Domain\Model\Firm\Team;
use Tests\TestBase;

class TeamParticipantTest extends TestBase
{
    protected $participant;
    protected $team;
    protected $teamParticipant;
    protected $id = "newId";
    protected $registrant;
    protected $meetingId = "meetingId", $meetingType, $meetingData;
    protected $mission, $missionComment, $missionCommentId = 'missionCommentId', $missionCommentData, $client;
    protected $participantAttendee, $task;
    protected $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->team = $this->buildMockOfClass(Team::class);
        $this->teamParticipant = new TestableTeamParticipant($this->participant, "id", $this->team);
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionComment = $this->buildMockOfClass(MissionComment::class);
        $this->missionCommentData = $this->buildMockOfClass(MissionCommentData::class);
        $this->client = $this->buildMockOfClass(Client::class);

        $this->participantAttendee = $this->buildMockOfClass(ParticipantAttendee::class);
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMeetingInitiator::class);
        
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    public function test_construct_setProperties()
    {
        $teamParticipant = new TestableTeamParticipant($this->participant, $this->id, $this->team);
        $this->assertEquals($this->participant, $teamParticipant->participant);
        $this->assertEquals($this->id, $teamParticipant->id);
        $this->assertEquals($this->team, $teamParticipant->team);
    }
    
    protected function executeAssertBelongsToTeam()
    {
        $this->teamParticipant->assertBelongsToTeam($this->team);
    }
    public function test_assertBelongsToTeam_sameTeam_void()
    {
        $this->executeAssertBelongsToTeam();
        $this->markAsSuccess();
    }
    public function test_assertBelongsToTeam_differentTeam_forbidden()
    {
        $this->teamParticipant->team = $this->buildMockOfClass(Team::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->executeAssertBelongsToTeam();
        }, 'Forbidden', 'forbidden: can only access owned asset');
    }
    
    public function test_correspondWithRegistrant_returnRegistrantCorrespondWithTeamResult()
    {
        $this->registrant->expects($this->once())
                ->method("correspondWithTeam")
                ->with($this->teamParticipant->team);
        $this->teamParticipant->correspondWithRegistrant($this->registrant);
    }
    
    protected function belongsToTeam()
    {
        return $this->teamParticipant->belongsToTeam($this->team);
    }
    public function test_belongsToTeam_sameTeam_returnTrue()
    {
        $this->assertTrue($this->belongsToTeam());
    }
    public function test_belongsToTeam_differentTeam_returnFalse()
    {
        $this->teamParticipant->team = $this->buildMockOfClass(Team::class);
        $this->assertFalse($this->belongsToTeam());
    }
    
    public function test_initiateMeeting_returnParticipantsInitiateMeetingResult()
    {
        $this->participant->expects($this->once())
                ->method("initiateMeeting")
                ->with($this->meetingId, $this->meetingType, $this->meetingData);
        $this->teamParticipant->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
    
    protected function executeSubmitCommentInMission()
    {
        $this->teamParticipant->submitCommentInMission($this->mission, $this->missionCommentId, $this->missionCommentData, $this->client);
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
                ->with('participant', $this->teamParticipant->id);
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
        $this->teamParticipant->replyMissionComment($this->missionComment, $this->missionCommentId, $this->missionCommentData, $this->client);
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
                ->with('participant', $this->teamParticipant->id);
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
    
    protected function executeTaskAsMeetingInitiator()
    {
        $this->teamParticipant->executeTaskAsMeetingInitiator($this->participantAttendee, $this->task);
    }
    public function test_executeTaskAsMeetingInitiator_participantAttendeeExecuteTask()
    {
        $this->participantAttendee->expects($this->once())
                ->method('executeTaskAsMeetingInitiator')
                ->with($this->task);
        $this->executeTaskAsMeetingInitiator();
    }
    public function test_executeTaskAsMeetingInitiator_assertBelongsParticipantAttendee()
    {
        $this->participantAttendee->expects($this->once())
                ->method('assertBelongsToParticipant')
                ->with($this->participant);
        $this->executeTaskAsMeetingInitiator();
    }
    
    protected function correspondWithProgram()
    {
        return $this->teamParticipant->correspondWithProgram($this->program);
    }
    public function test_correspondWithProgram_returnParticipantsProgramCorrepondencyResult()
    {
        $this->participant->expects($this->once())
                ->method('correspondWithProgram')
                ->with($this->program);
        $this->correspondWithProgram();
    }
    
    protected function enable()
    {
        $this->teamParticipant->enable();
    }
    public function test_enable_reenrollParticipnat()
    {
        $this->participant->expects($this->once())
                ->method('reenroll');
        $this->enable();
    }
    
    //
    protected function isActiveParticipantInProgram()
    {
        return $this->teamParticipant->isActiveParticipantInProgram($this->program);
    }
    public function test_isActiveParticipantInProgram_returnParticipantEvaluationResult()
    {
        $this->participant->expects($this->once())
                ->method('isActiveParticipantInProgram')
                ->with($this->program);
        $this->isActiveParticipantInProgram();
    }
}

class TestableTeamParticipant extends TeamParticipant
{
    public $participant;
    public $id;
    public $team;
}
