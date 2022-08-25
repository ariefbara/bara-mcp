<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;
use Firm\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Firm\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Firm\Domain\Model\Firm\Program\Participant\Evaluation;
use Firm\Domain\Model\Firm\Program\Participant\EvaluationData;
use Firm\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantProfile;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Model\User;
use Firm\Domain\Service\MetricAssignmentDataProvider;
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use Tests\TestBase;

class ParticipantTest extends TestBase
{

    protected $program;
    protected $participant;
    protected $participantAttendee;
    protected $asset;
    protected $consultationRequest;
    protected $consultationSession;
    protected $invitation;
    protected $inactiveParticipant;
    protected $clientParticipant;
    protected $userParticipant;
    protected $teamParticipant;
    protected $id = 'newParticipantId', $user, $client, $teamId = "teamId";
    protected $registrant;
    protected $metricAssignment;
    protected $metricAssignmentDataProvider;
    protected $metric;
    protected $meetingId = "meetingId", $meetingType, $meetingData;
    protected $team;
    protected $evaluation;
    protected $evaluationPlan, $coordinator, $evaluationData;
    protected $programsProfileForm, $formRecord;
    protected $consultant;
    protected $dedicatedMentor;
    protected $meeting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);

        $this->participant = new TestableParticipant($this->program, 'id');
        $this->participant->consultationRequests = new ArrayCollection();
        $this->participant->consultationSessions = new ArrayCollection();
        $this->participant->meetingInvitations = new ArrayCollection();
        $this->participant->dedicatedMentors = new ArrayCollection();
        
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->participant->consultationRequests->add($this->consultationRequest);
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->participant->consultationSessions->add($this->consultationSession);
        $this->invitation = $this->buildMockOfClass(ParticipantAttendee::class);
        $this->participant->meetingInvitations->add($this->invitation);
        $this->dedicatedMentor = $this->buildMockOfClass(DedicatedMentor::class);
        $this->participant->dedicatedMentors->add($this->dedicatedMentor);
        
        $this->participant->evaluations = new ArrayCollection();
        
        $this->asset = $this->buildMockOfInterface(AssetInProgram::class);
        
        $this->inactiveParticipant = new TestableParticipant($this->program, 'id');
        $this->inactiveParticipant->active = false;
        $this->inactiveParticipant->note = 'booted';
        
        $this->user = $this->buildMockOfClass(User::class);
        $this->client = $this->buildMockOfClass(Client::class);

        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->participant->clientParticipant = $this->clientParticipant;

        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->inactiveParticipant->userParticipant = $this->userParticipant;

        $this->registrant = $this->buildMockOfClass(Registrant::class);

        $this->teamParticipant = $this->buildMockOfClass(TeamParticipant::class);

        $this->metricAssignment = $this->buildMockOfClass(MetricAssignment::class);
        $this->metricAssignmentDataProvider = $this->buildMockOfClass(MetricAssignmentDataProvider::class);
        $this->metricAssignmentDataProvider->expects($this->any())->method("getStartDate")->willReturn(new DateTimeImmutable("+1 days"));
        $this->metricAssignmentDataProvider->expects($this->any())->method("getEndDate")->willReturn(new DateTimeImmutable("+2 days"));
        
        $this->metric = $this->buildMockOfClass(Metric::class);
        
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        
        $this->team = $this->buildMockOfClass(Team::class);
        
        $this->evaluation = $this->buildMockOfClass(Evaluation::class);
        $this->participant->evaluations->add($this->evaluation);
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationData = $this->buildMockOfClass(EvaluationData::class);
        $this->evaluationData->expects($this->any())->method("getStatus")->willReturn("pass");
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        
        $this->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        
        $this->meeting = $this->buildMockOfClass(Meeting::class);
    }
    protected function assertInactiveParticipant(callable $operation): void
    {
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: inactive partiicpant');
    }
    
    protected function construct()
    {
        return new TestableParticipant($this->program, $this->id);
    }
    public function test_construct_setProperties()
    {
        $participant = $this->construct();
        $this->assertSame($this->program, $participant->program);
        $this->assertSame($this->id, $participant->id);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $participant->enrolledTime);
    }
    public function test_construct_recordProgramParticipationAcceptedCommonEvent()
    {
        $event = new \Resources\Domain\Event\CommonEvent(\Config\EventList::PROGRAM_PARTICIPATION_ACCEPTED, $this->id);
        $participant = $this->construct();
        $this->assertEquals($event, $participant->recordedEvents[0]);
    }

    public function test_participantForUser_setProperties()
    {
        $participant = TestableParticipant::participantForUser($this->program, $this->id, $this->user);
        $this->assertEquals($this->program, $participant->program);
        $this->assertEquals($this->id, $participant->id);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $participant->enrolledTime);
        $this->assertTrue($participant->active);
        $this->assertNull($participant->note);

        $userParticipant = new UserParticipant($participant, $this->id, $this->user);
        $this->assertEquals($userParticipant, $participant->userParticipant);
        $this->assertNull($participant->clientParticipant);
    }

    public function test_participantForClient_setProperties()
    {
        $participant = TestableParticipant::participantForClient($this->program, $this->id, $this->client);
        $this->assertEquals($this->program, $participant->program);
        $this->assertEquals($this->id, $participant->id);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $participant->enrolledTime);
        $this->assertTrue($participant->active);
        $this->assertNull($participant->note);

        $clientParticipant = new ClientParticipant($participant, $this->id, $this->client);
        $this->assertEquals($clientParticipant, $participant->clientParticipant);
        $this->assertNull($participant->userParticipant);
    }

    public function test_participantForTeam_setProperties()
    {
        $participant = TestableParticipant::participantForTeam($this->program, $this->id, $this->team);
        $this->assertEquals($this->program, $participant->program);
        $this->assertEquals($this->id, $participant->id);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $participant->enrolledTime);
        $this->assertTrue($participant->active);
        $this->assertNull($participant->note);

        $teamParticipant = new TeamParticipant($participant, $this->id, $this->team);
        $this->assertEquals($teamParticipant, $participant->teamParticipant);
        $this->assertNull($participant->userParticipant);
        $this->assertNull($participant->clientParticipant);
    }
    
    public function test_asserActive_activeParticipant_void()
    {
        $this->participant->assertActive();
        $this->markAsSuccess();
    }
    public function test_asserActive_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertInactiveParticipant(function(){
            $this->participant->assertActive();
        });
    }
    
    public function test_assertAssetAccessible_inaccesibleAsset_forbidden()
    {
        $this->assertRegularExceptionThrowed(function (){
            $this->participant->assertAssetAccessible($this->asset);
        }, 'Forbidden', 'forbidden: unable to access asset not in same program');
    }
    public function test_assertAssetAccessible_accessibleAsset_void()
    {
        $this->asset->expects($this->once())
                ->method('belongsToProgram')
                ->with($this->participant->program)
                ->willReturn(true);
        $this->participant->assertAssetAccessible($this->asset);
    }

    public function test_belongsToProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->participant->belongsToProgram($this->participant->program));
    }
    public function test_belongsToProgram_differentprogram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->participant->belongsToProgram($program));
    }

    protected function executeReenroll()
    {
        $this->inactiveParticipant->reenroll();
    }
    public function test_reenroll_setActiveTrueAndNulledNote()
    {
        $this->executeReenroll();
        $this->assertTrue($this->inactiveParticipant->active);
        $this->assertNull($this->inactiveParticipant->note);
    }
    public function test_reenroll_activeParticipant_forbiddenError()
    {
        $operation = function () {
            $this->participant->reenroll();
        };
        $errorDetail = 'forbidden: already active participant';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    protected function executeCorrespondWithRegistrant()
    {
        return $this->participant->correspondWithRegistrant($this->registrant);
    }
    public function test_correspondWithRegistrant_returnClientParticipantsCorrespondWithRegistrantResult()
    {
        $this->clientParticipant->expects($this->once())
                ->method('correspondWithRegistrant');
        $this->executeCorrespondWithRegistrant();
    }
    public function test_correspondWithRegistrant_aUserParticipant_returnUserParticipantCorrespondWithRegistrantRegsult()
    {
        $this->participant->clientParticipant = null;
        $this->participant->userParticipant = $this->userParticipant;

        $this->userParticipant->expects($this->once())
                ->method('correspondWithRegistrant');
        $this->executeCorrespondWithRegistrant();
    }
    public function test_correspondWithRegistrant_aTeamParticipant_returnTeamParticipantCorrespondWithRegistrantResult()
    {
        $this->participant->clientParticipant = null;
        $this->participant->teamParticipant = $this->teamParticipant;

        $this->teamParticipant->expects($this->once())
                ->method("correspondWithRegistrant");
        $this->executeCorrespondWithRegistrant();
    }

    protected function executeAssignMetrics()
    {
        $this->participant->assignMetrics($this->metricAssignmentDataProvider);
    }
    public function test_assignMetric_addMetricAssignment()
    {
        $this->executeAssignMetrics();
        $this->assertInstanceOf(MetricAssignment::class, $this->participant->metricAssignment);
    }
    public function test_assignMetric_alreadyHasMetricAssignment_updateExistingMetricAssignment()
    {
        $this->participant->metricAssignment = $this->metricAssignment;
        $this->metricAssignment->expects($this->once())
                ->method("update")
                ->with($this->metricAssignmentDataProvider);
        $this->executeAssignMetrics();
    }
    public function test_assignMetric_alreadyHasMetricAssignment_avoidAddNewAssignment()
    {
        $this->participant->metricAssignment = $this->metricAssignment;
        $this->executeAssignMetrics();
        $this->assertEquals($this->metricAssignment, $this->participant->metricAssignment);
    }
    
    public function test_belongsInTheSameProgramAs_returnMetricsBelongsToProgramResult()
    {
        $this->metric->expects($this->once())
                ->method("belongsToProgram");
        $this->participant->belongsInTheSameProgramAs($this->metric);
    }
    
    public function test_belongsToTeam_returnTeamParticipantBelongsToTeamResult()
    {
        $this->participant->teamParticipant = $this->teamParticipant;
        $this->teamParticipant->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->team);
        $this->participant->belongsToTeam($this->team);
    }
    public function test_belongsToTeam_notATeamParticipant_returnFalse()
    {
        $this->participant->teamParticipant = null;
        $this->assertFalse($this->participant->belongsToTeam($this->team));
    }
    
    protected function executeReceiveEvaluation()
    {
        $this->participant->receiveEvaluation($this->evaluationPlan, $this->evaluationData, $this->coordinator);
    }
    public function test_receiveEvaluation_addEvaluationToCollection()
    {
        $this->executeReceiveEvaluation();
        $this->assertEquals(2, $this->participant->evaluations->count());
        $this->assertInstanceOf(Evaluation::class, $this->participant->evaluations->last());
    }
    public function test_receiveEvaluation_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $operation = function (){
            $this->executeReceiveEvaluation();
        };
        $errorDetail = "forbidden: unable to evaluate inactive participant";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_receiveEvaluation_alreadyReceiveConcludedEvaluationForSamePlan_forbidden()
    {
        $this->evaluation->expects($this->once())
                ->method("isCompletedEvaluationForPlan")
                ->with($this->evaluationPlan)
                ->willReturn(true);
        $operation = function (){
            $this->executeReceiveEvaluation();
        };
        $errorDetail = "forbidden: participant already completed evaluation for this plan";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeQualify()
    {
        $this->participant->qualify();
    }
    public function test_qualify_setInactiveAndNoteQualified()
    {
        $this->executeQualify();
        $this->assertFalse($this->participant->active);
        $this->assertEquals("completed", $this->participant->note);
    }
    public function test_qualify_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $operation = function (){
            $this->executeQualify();
        };
        $errorDetail = "forbidden: unable to qualify inactive participant";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeDisable()
    {
        $this->participant->disable();
    }
    public function test_disable_setInactiveAndNote()
    {
        $this->executeDisable();
        $this->assertFalse($this->participant->active);
        $this->assertEquals("fail", $this->participant->note);
    }
    public function test_disable_alreadyInactive_forbidden()
    {
        $this->participant->active = false;
        $operation = function (){
            $this->executeDisable();
        };
        $errorDetail = "forbidden: unable to disable inactive participant";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_disable_disableUpcomingConsultationSession()
    {
        $this->consultationSession->expects($this->once())
                ->method("disableUpcomingSession");
        $this->executeDisable();
    }
    public function test_disable_disableUpcomingConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
                ->method("disableUpcomingRequest");
        $this->executeDisable();
    }
    public function test_disable_disableValidInvitation()
    {
        $this->invitation->expects($this->once())
                ->method("disableValidInvitation");
        $this->executeDisable();
    }
    
    public function test_addProfile_addProfileToCollection()
    {
        $this->formRecord->expects($this->once())
                ->method("getId")->willReturn($formRecordId = "formRecordId");
        $profile = new ParticipantProfile($this->participant, $formRecordId, $this->programsProfileForm, $this->formRecord);
        
        $this->participant->addProfile($this->programsProfileForm, $this->formRecord);
        $this->assertEquals($profile, $this->participant->profiles->first());
    }
    
    protected function executeDedicateMentor()
    {
        $this->consultant->expects($this->any())
                ->method('isActive')
                ->willReturn(true);
        return $this->participant->dedicateMentor($this->consultant);
    }
    public function test_dedicateMentor_addDedicatedMentorToCollection()
    {
        $this->executeDedicateMentor();
        $this->assertEquals(2, $this->participant->dedicatedMentors->count());
        $this->assertInstanceOf(DedicatedMentor::class, $this->participant->dedicatedMentors->last());
    }
    public function test_dedicateMentor_consultantAlreadyParticipantDedicatedMentor_reassignExistingDedicatedMentor()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('consultantEquals')
                ->with($this->consultant)
                ->willReturn(true);
        $this->dedicatedMentor->expects($this->once())
                ->method('reassign');
        $this->executeDedicateMentor();
    }
    public function test_dedicateMentor_alreadyADedicatedMentor_preventAddNewDedicatedMentorToCollection()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('consultantEquals')
                ->with($this->consultant)
                ->willReturn(true);
        $this->executeDedicateMentor();
        $this->assertEquals(1, $this->participant->dedicatedMentors->count());
    }
    public function test_dedicatementor_returnDedicatedMentorId()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('consultantEquals')
                ->with($this->consultant)
                ->willReturn(true);
        $this->dedicatedMentor->expects($this->once())->method('getId')
                ->willReturn($dedicatedMentorId = 'dedicatedMentorId');
        $this->assertEquals($dedicatedMentorId, $this->executeDedicateMentor());
    }
    public function test_dedicateMentor_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertInactiveParticipant(function (){
            $this->executeDedicateMentor();
        });
    }
    
    public function test_isActive_activeParticipant_returnTrue()
    {
        $this->assertTrue($this->participant->isActive());
    }
    public function test_isActive_inactiveParticipant_returnFalse()
    {
        $this->participant->active = false;
        $this->assertFalse($this->participant->isActive());
    }
    
    protected function executeInitiateMeeting()
    {
        return $this->participant->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
    public function test_initiateMeeting_returnMeetingCreatedInActivityType()
    {
        $this->meetingType->expects($this->once())
                ->method('createMeeting')
                ->with($this->meetingId, $this->meetingData)
                ->willReturn($meeting = $this->buildMockOfClass(Meeting::class));
        $this->assertEquals($meeting, $this->executeInitiateMeeting());
    }
    public function test_initiateMeeting_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeInitiateMeeting();
        }, 'Forbidden', 'forbidden: inactive partiicpant');
    }
    public function test_initiateMeeting_assertActivityTypeUsableInProgram()
    {
        $this->meetingType->expects($this->once())
                ->method('assertUsableInProgram')
                ->with($this->program);
        $this->executeInitiateMeeting();
    }
    public function test_initiateMeeting_aggregateParticipantAttendeeToMeetingInvitationCollection()
    {
        $this->executeInitiateMeeting();
        $this->assertEquals(2, $this->participant->meetingInvitations->count());
        $this->assertInstanceOf(ParticipantAttendee::class, $this->participant->meetingInvitations->last());
    }
    
    protected function executeInviteToMeeting()
    {
        $this->participant->inviteToMeeting($this->meeting);
    }
    public function test_inviteToMeeting_addNewParticipantAttendeeToMeetingInvitationCollection()
    {
        $this->executeInviteToMeeting();
        $this->assertEquals(2, $this->participant->meetingInvitations->count());
        $this->assertInstanceOf(ParticipantAttendee::class, $this->participant->meetingInvitations->last());
    }
    public function test_inviteToMeeting_hasActiveInvitationToSameMeeting_void()
    {
        $this->invitation->expects($this->once())
                ->method('isActiveAttendeeOfMeeting')
                ->with($this->meeting)
                ->willReturn(true);
        $this->executeInviteToMeeting();
        $this->assertEquals(1, $this->participant->meetingInvitations->count());
    }
    public function test_inviteToMeeting_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertInactiveParticipant(function (){
            $this->executeInviteToMeeting();
        });
    }
    public function test_inviteToMeeting_assertMeetingUsableInProgram()
    {
        $this->meeting->expects($this->once())
                ->method('assertUsableInProgram')
                ->with($this->program);
        $this->executeInviteToMeeting();
    }
    
    protected function correspondWithProgram()
    {
        return $this->participant->correspondWithProgram($this->program);
    }
    public function test_correspondWithProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->correspondWithProgram());
    }
    public function test_correspondWithProgram_differentProgram_returnFalse()
    {
        $this->participant->program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->correspondWithProgram());
    }
    
    //
    protected function isActiveParticipantInProgram()
    {
        return $this->participant->isActiveParticipantInProgram($this->program);
    }
    public function test_isActiveParticipantInProgram_activeParticipantInSameProgram_returnTrue()
    {
        $this->assertTrue($this->isActiveParticipantInProgram());
    }
    public function test_isActiveParticipantInProgram_inactiveParticipant_returnFalse()
    {
        $this->participant->active = false;
        $this->assertFalse($this->isActiveParticipantInProgram());
    }
    public function test_isActiveParticipantInProgram_differentProgram_returnFalse()
    {
        $this->participant->program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->isActiveParticipantInProgram());
    }
    
}

class TestableParticipant extends Participant
{

    public $program;
    public $id;
    public $enrolledTime;
    public $active = true;
    public $note;
    public $clientParticipant;
    public $userParticipant;
    public $teamParticipant;
    public $metricAssignment;
    public $evaluations;
    public $profiles;
    public $meetingInvitations;
    public $consultationRequests;
    public $consultationSessions;
    public $dedicatedMentors;
    //
    public $recordedEvents;

    public function __construct(Program $program, string $id)
    {
        parent::__construct($program, $id);
    }

}
