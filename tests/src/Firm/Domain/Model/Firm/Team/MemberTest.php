<?php

namespace Firm\Domain\Model\Firm\Team;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\CanAttendMeeting;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Firm\Domain\Model\Firm\Program\TeamParticipant;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class MemberTest extends TestBase
{
    protected $team;
    protected $client;
    protected $member;
    
    protected $id = 'newId', $position = 'new position';

    protected $meetingId = "meetingId", $teamParticipant, $meetingType, $meetingData;
    protected $attendeeFinder, $attendee;
    protected $user;
    protected $toCancelAttendee;
    protected $mission, $missionComment, $missionCommentId = 'missionCommentId', $missionCommentData;
    
    protected $participantAttendee, $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->team = $this->buildMockOfClass(Team::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $memberData = new MemberData($this->client, 'position');
        $this->member = new TestableMember($this->team, 'id', $memberData);
        
        $this->teamParticipant = $this->buildMockOfClass(TeamParticipant::class);
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeFinder = $this->buildMockOfClass(MeetingAttendeeBelongsToTeamFinder::class);
        $this->attendeeFinder->expects($this->any())
                ->method("execute")
                ->with($this->team, $this->meetingId)
                ->willReturn($this->attendee);
        
        $this->toCancelAttendee = $this->buildMockOfClass(Attendee::class);
        
        $this->user = $this->buildMockOfInterface(CanAttendMeeting::class);
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionComment = $this->buildMockOfClass(MissionComment::class);
        $this->missionCommentData = $this->buildMockOfClass(MissionCommentData::class);
        
        $this->participantAttendee = $this->buildMockOfClass(ParticipantAttendee::class);
        $this->task = $this->buildMockOfClass(ITaskExecutableByMeetingInitiator::class);
    }
    
    protected function getMemberData()
    {
        return new MemberData($this->client, $this->position);
    }
    
    protected function construct()
    {
        return new TestableMember($this->team, $this->id, $this->getMemberData());
    }
    public function test_construct_setProperties()
    {
        $member = $this->construct();
        $this->assertSame($this->team, $member->team);
        $this->assertSame($this->id, $member->id);
        $this->assertSame($this->client, $member->client);
        $this->assertTrue($member->anAdmin);
        $this->assertTrue($member->active);
        $this->assertSame($this->position, $member->position);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $member->joinTime);
    }
    
    protected function setAttendeeDoesntBelongsToTeam()
    {
        $this->attendee->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->team)
                ->willReturn(false);
    }
    
    protected function assertInactiveMemberForbiddenError(callable $operation)
    {
        $errorDetail = "forbidden: only active team member can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    protected function assertAttendaceDoesntBelongsToTeamForbiddenError(callable $operation)
    {
        $errorDetail = "forbidden: meeting attendance doesnt belongs to your team";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeInitiateMeeting()
    {
        $this->teamParticipant->expects($this->any())
                ->method("belongsToTeam")
                ->willReturn(true);
        $this->member->initiateMeeting($this->meetingId, $this->teamParticipant, $this->meetingType, $this->meetingData);
    }
    public function test_initiateMeeting_returnTeamParticipantInitiateMeetingResult()
    {
        $this->teamParticipant->expects($this->once())
                ->method("initiateMeeting")
                ->with($this->meetingId, $this->meetingType, $this->meetingData);
        $this->executeInitiateMeeting();
    }
    public function test_initiateMeeting_inactiveMember_forbidden()
    {
        $this->member->active = false;
        $operation  = function (){
            $this->executeInitiateMeeting();
        };
        $this->assertInactiveMemberForbiddenError($operation);
    }
    public function test_initiateMeeting_teamParticipantDoesntBelongsToTeam_forbidden()
    {
        $this->teamParticipant->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->team)
                ->willReturn(false);
        $operation  = function (){
            $this->executeInitiateMeeting();
        };
        $errorDetail = "forbidden: can only manage program participation belongs to same team";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
/*
    protected function executeUpdateMeeting()
    {
        $this->member->updateMeeting($this->attendeeFinder, $this->meetingId, $this->meetingData);
    }
    public function test_updateMeeting_executeAttendeeUpdateMeetings()
    {
        $this->attendee->expects($this->once())
                ->method("updateMeeting")
                ->with($this->meetingData);
        $this->executeUpdateMeeting();
    }
    public function test_updateMeeting_inactiveMember_forbidden()
    {
        $this->member->active = false;
        $this->assertInactiveMemberForbiddenError(function (){
            $this->executeUpdateMeeting();
        });
    }
    public function test_updateMeeting_collectEventsFromAttendee()
    {
        $this->attendee->expects($this->once())
                ->method("pullRecordedEvents")
                ->willReturn($recordedEvents = ["array represent recorded event list"]);
        $this->executeUpdateMeeting();
        $this->assertEquals($recordedEvents, $this->member->recordedEvents);
    }
    
    protected function executeInviteUserToAttendMeeting()
    {
        $this->member->inviteUserToAttendMeeting($this->attendeeFinder, $this->meetingId, $this->user);
    }
    public function test_inviteUserToAttendMeeting_execteAttendeesInviteUserToAttendMeeting()
    {
        $this->attendee->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->user);
        $this->executeInviteUserToAttendMeeting();
    }
    public function test_inviteUserToAttendMeeting_inactiveMember_forbidden()
    {
        $this->member->active = false;
        $this->assertInactiveMemberForbiddenError(function (){
            $this->executeInviteUserToAttendMeeting();
        });
    }
    public function test_inviteUserToAttendMeeting_collectEventsFromAttendee()
    {
        $this->attendee->expects($this->once())
                ->method("pullRecordedEvents")
                ->willReturn($recordedEvents = ["array represent recorded event list"]);
        $this->executeInviteUserToAttendMeeting();
        $this->assertEquals($recordedEvents, $this->member->recordedEvents);
    }
    
    protected function executeCancelInvitation()
    {
        $this->member->cancelInvitation($this->attendeeFinder, $this->meetingId, $this->toCancelAttendee);
    }
    public function test_cancelInvitation_execteAttendeesCancelInvitation()
    {
        $this->attendee->expects($this->once())
                ->method("cancelInvitationTo")
                ->with($this->toCancelAttendee);
        $this->executeCancelInvitation();
    }
    public function test_cancelInvitation_inactiveMember_forbidden()
    {
        $this->member->active = false;
        $this->assertInactiveMemberForbiddenError(function (){
            $this->executeCancelInvitation();
        });
    }
 * 
 */
    
    protected function executeSubmitCommentInMission()
    {
        $this->teamParticipant->expects($this->any())
                ->method('belongsToTeam')
                ->willReturn(true);
        $this->member->submitCommentInMission(
                $this->teamParticipant, $this->mission, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_submitCommentInMission_returnTeamParticipantsSubmitCommentInMissionResult()
    {
        $this->teamParticipant->expects($this->once())
                ->method('submitCommentInMission')
                ->with($this->mission, $this->missionCommentId, $this->missionCommentData, $this->member->client);
        $this->executeSubmitCommentInMission();
    }
    public function test_submitCommentInMission_modifyMissionCommentData()
    {
        $this->missionCommentData->expects($this->once())
                ->method('addRolePath')
                ->with('member', $this->member->id);
        $this->executeSubmitCommentInMission();
    }
    public function test_submitComment_inactiveMember_forbidden()
    {
        $this->member->active = false;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeSubmitCommentInMission();
        }, 'Forbidden', 'forbidden: only active team member can make this request');
    }
    public function test_submitComment_unmanagedTeamParticipant_forbidden()
    {
        $this->teamParticipant->expects($this->once())
                ->method('belongsToTeam')
                ->with($this->member->team)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeSubmitCommentInMission();
        }, 'Forbidden', 'forbidden: unable to manage team participant');
    }
    
    protected function executeReplyMissionComment()
    {
        $this->teamParticipant->expects($this->any())
                ->method('belongsToTeam')
                ->willReturn(true);
        $this->member->replyMissionComment(
                $this->teamParticipant, $this->missionComment, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_replyMissionComment_returnTeamParticipantsReplyMissionCommentResult()
    {
        $this->teamParticipant->expects($this->once())
                ->method('replyMissionComment')
                ->with($this->missionComment, $this->missionCommentId, $this->missionCommentData, $this->member->client);
        $this->executeReplyMissionComment();
    }
    public function test_replyMissionComment_modifyMissionCommentData()
    {
        $this->missionCommentData->expects($this->once())
                ->method('addRolePath')
                ->with('member', $this->member->id);
        $this->executeReplyMissionComment();
    }
    public function test_replyMissionComment_inactiveMember_forbidden()
    {
        $this->member->active = false;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeReplyMissionComment();
        }, 'Forbidden', 'forbidden: only active team member can make this request');
    }
    public function test_replyMissionComment_unmanagedTeamParticipant_forbidden()
    {
        $this->teamParticipant->expects($this->once())
                ->method('belongsToTeam')
                ->with($this->member->team)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeReplyMissionComment();
        }, 'Forbidden', 'forbidden: unable to manage team participant');
    }
    
    protected function executeTaskAsMemberOfTeamParticipantMeetingInitiator()
    {
        $this->member->executeTaskAsMemberOfTeamParticipantMeetingInitiator(
                $this->teamParticipant, $this->participantAttendee, $this->task);
    }
    public function test_executeTaskAsMemberOfTeamParticipantMeetingInitiator_teamParticipantExecuteTaskAsMeetingInitiator()
    {
        $this->teamParticipant->expects($this->once())
                ->method('executeTaskAsMeetingInitiator')
                ->with($this->participantAttendee, $this->task);
        $this->executeTaskAsMemberOfTeamParticipantMeetingInitiator();
    }
    public function test_executeTaskAsMeetingInitiator_inactiveMembet_forbidden()
    {
        $this->member->active = false;
        $this->assertInactiveMemberForbiddenError(function (){
            $this->executeTaskAsMemberOfTeamParticipantMeetingInitiator();
        });
    }
    public function test_executeTaskAsMeetingInitiator_assertTeamParticipantBelongsToTeam()
    {
        $this->teamParticipant->expects($this->once())
                ->method('assertBelongsToTeam')
                ->with($this->team);
        $this->executeTaskAsMemberOfTeamParticipantMeetingInitiator();
    }
    
    protected function correspondWithClient()
    {
        return $this->member->correspondWithClient($this->client);
    }
    public function test_correspondWithClient_sameClient_returnTrue()
    {
        $this->assertTrue($this->correspondWithClient());
    }
    public function test_correspondWithClient_differentClient_returnFalse()
    {
        $this->member->client = $this->buildMockOfClass(Client::class);
        $this->assertFalse($this->correspondWithClient());
    }
    
    protected function enable()
    {
        $this->member->enable($this->position);
    }
    public function test_enable_setActiveAndUpdatePosition()
    {
        $this->member->active = false;
        $this->enable();
        $this->assertTrue($this->member->active);
        $this->assertSame($this->position, $this->member->position);
    }
    public function test_enable_alreadyActiveMember_forbidden()
    {
        $this->assertRegularExceptionThrowed(function() {
            $this->enable();
        }, 'Forbidden', 'forbidden: already active member');
    }
    
    protected function disable()
    {
        $this->member->disable();
    }
    public function test_disable_setInactive()
    {
        $this->disable();
        $this->assertFalse($this->member->active);
    }
    public function test_disable_alreadyInactiveMember_forbidden()
    {
        $this->member->active = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->disable();
        }, 'Forbidden', 'forbidden: already inactive member');
    }
    
}

class TestableMember extends Member
{
    public $team;
    public $id;
    public $client;
    public $anAdmin;
    public $active;
    public $position;
    public $joinTime;
}
