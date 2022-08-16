<?php

namespace Firm\Domain\Model\Firm\Program;

use Config\EventList;
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
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\ValueObject\ParticipantStatus;
use Tests\TestBase;

class ParticipantTest extends TestBase
{

    protected $program, $programAutoAccept = false, $programPrice = 50000;
    protected $participant, $status;
    protected $participantAttendee;
    protected $asset;
    protected $consultationRequest;
    protected $consultationSession;
    protected $invitation;
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
        
        $this->participant = new TestableParticipant($this->program, 'id', false, null);
        $this->status = $this->buildMockOfClass(ParticipantStatus::class);
        $this->participant->status = $this->status;
        
        $this->participant->recordedEvents = [];
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
        
        $this->participant->profiles = new ArrayCollection();
        $this->participant->evaluations = new ArrayCollection();
        
        $this->asset = $this->buildMockOfInterface(AssetInProgram::class);
        
        $this->user = $this->buildMockOfClass(User::class);
        $this->client = $this->buildMockOfClass(Client::class);

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
        return new TestableParticipant(
                $this->program, $this->id, $this->programAutoAccept, $this->programPrice);
    }
    public function test_construct_setProperties()
    {
        $participant = $this->construct();
        $this->assertSame($this->program, $participant->program);
        $this->assertSame($this->id, $participant->id);
        $this->assertSame($this->programPrice, $participant->programPrice);
        $status = new ParticipantStatus($this->programAutoAccept, $this->programPrice);
        $this->assertEquals($status, $participant->status);
    }
    public function test_construct_recordProgramParticipationAcceptedCommonEvent()
    {
        $event = new CommonEvent(EventList::PROGRAM_APPLICATION_RECEIVED, $this->id);
        $participant = $this->construct();
        $this->assertEquals($event, $participant->recordedEvents[0]);
    }
    public function test_construct_needSettlementStatus_recordSettlementRequiredEvent()
    {
        $this->programAutoAccept = true;
        $participant = $this->construct();
        
        $event = new CommonEvent(EventList::SETTLEMENT_REQUIRED, $this->id);
        $this->assertEquals($event, $participant->recordedEvents[1]);
    }
    public function test_construct_nonSettlementStatus()
    {
        $participant = $this->construct();
        $event = new CommonEvent(EventList::PROGRAM_APPLICATION_RECEIVED, $this->id);
        $this->assertEquals($event, $participant->recordedEvents[0]);
        $this->assertEquals(1, count($participant->recordedEvents));
    }
    
    protected function acceptRegistrant()
    {
        $this->participant->acceptRegistrant();
    }
    public function test_acceptRegistrant_updateStatusAccepted()
    {
        $this->status->expects($this->once())
                ->method('acceptRegistrant')
                ->with($this->participant->programPrice)
                ->willReturn($status = $this->buildMockOfClass(ParticipantStatus::class));
        $this->acceptRegistrant();
        $this->assertSame($status, $this->participant->status);
    }
    public function test_acceptRegistrant_paidProgram_recordSettlementRequiredEvent()
    {
        $this->status->expects($this->once())
                ->method('acceptRegistrant')
                ->willReturn($status = $this->buildMockOfClass(ParticipantStatus::class));
        $status->expects($this->any())
                ->method('statusEquals')
                ->with(ParticipantStatus::SETTLEMENT_REQUIRED)
                ->willReturn(true);
        $event = new CommonEvent(EventList::SETTLEMENT_REQUIRED, $this->participant->id);
        $this->acceptRegistrant();
        $this->assertEquals($event, $this->participant->recordedEvents[0]);
    }
    public function test_acceptRegistrant_freeProgram()
    {
        $this->acceptRegistrant();
        $this->assertEmpty($this->participant->recordedEvents);
    }
    
    protected function rejectRegistrant()
    {
        $this->participant->rejectRegistrant();
    }
    public function test_rejectRegistrant_updateStatusRejected()
    {
        $this->status->expects($this->once())
                ->method('rejectRegistrant')
                ->willReturn($status = $this->buildMockOfClass(ParticipantStatus::class));
        $this->rejectRegistrant();
        $this->assertSame($status, $this->participant->status);
    }
    
    protected function qualify()
    {
        $this->participant->qualify();
    }
    public function test_qualify_updateStatusCompleted()
    {
        $this->status->expects($this->once())
                ->method('qualify')
                ->willReturn($newStatus = $this->buildMockOfClass(ParticipantStatus::class));
        $this->qualify();
        $this->assertSame($newStatus, $this->participant->status);
    }

    protected function assertActive()
    {
        $this->status->expects($this->any())
                ->method('statusEquals')
                ->with(ParticipantStatus::ACTIVE)
                ->willReturn(true);
        $this->participant->assertActive();
    }
    public function test_asserActive_activeParticipant_void()
    {
        $this->assertActive();
        $this->markAsSuccess();
    }
    public function test_asserActive_inactiveParticipant_forbidden()
    {
        $this->status->expects($this->any())
                ->method('statusEquals')
                ->with(ParticipantStatus::ACTIVE)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertActive();
        }, 'Forbidden', 'inactive participant');
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
    
    protected function executeReceiveEvaluation()
    {
        $this->status->expects($this->any())
                ->method('statusEquals')
                ->with(ParticipantStatus::ACTIVE)
                ->willReturn(true);
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
        $this->status->expects($this->any())
                ->method('statusEquals')
                ->with(ParticipantStatus::ACTIVE)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->executeReceiveEvaluation();
        }, 'Forbidden', 'inactive participant');
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
    
    protected function executeFail()
    {
        $this->participant->fail();
    }
    public function test_fail_updateStatusFailed()
    {
        $this->status->expects($this->once())
                ->method('fail')
                ->willReturn($status = $this->buildMockOfClass(ParticipantStatus::class));
        $this->executeFail();
        $this->assertSame($status, $this->participant->status);
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
        $this->status->expects($this->any())
                ->method('statusEquals')
                ->willReturn(true);
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
        $this->status->expects($this->any())
                ->method('statusEquals')
                ->with(ParticipantStatus::ACTIVE)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->executeDedicateMentor();
        }, 'Forbidden', 'inactive participant');
    }
    
    protected function executeInitiateMeeting()
    {
        $this->status->expects($this->any())
                ->method('statusEquals')
                ->willReturn(true);
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
        $this->status->expects($this->any())
                ->method('statusEquals')
                ->with(ParticipantStatus::ACTIVE)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->executeInitiateMeeting();
        }, 'Forbidden', 'inactive participant');
        
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
        $this->status->expects($this->any())
                ->method('statusEquals')
                ->willReturn(true);
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
        $this->status->expects($this->any())
                ->method('statusEquals')
                ->with(ParticipantStatus::ACTIVE)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->executeInviteToMeeting();
        }, 'Forbidden', 'inactive participant');
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
    
    protected function isActiveParticipantOrRegistrantOfProgram()
    {
        $this->status->expects($this->any())
                ->method('isActiveRegistrantOrParticipant')
                ->willReturn(true);
        return $this->participant->isActiveParticipantOrRegistrantOfProgram($this->program);
    }
    public function test_isActiveParticipantOrRegistrantOfProgram_activeRegistrantCorrespondToSameProgram_returnTrue()
    {
        $this->assertTrue($this->isActiveParticipantOrRegistrantOfProgram());
    }
    public function test_isActiveParticipantOrRegistrantOfProgram_differentProgram_returnFalse()
    {
        $this->participant->program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->isActiveParticipantOrRegistrantOfProgram());
    }
    public function test_isActiveParticipantOrRegistrantOfProgram_inactiveRegistrantOrParticipant_returnFalse()
    {
        $this->status->expects($this->any())
                ->method('isActiveRegistrantOrParticipant')
                ->willReturn(false);
        $this->assertFalse($this->isActiveParticipantOrRegistrantOfProgram());
    }
    
    protected function assertManageableInProgram()
    {
        $this->participant->assertManageableInProgram($this->program);
    }
    public function test_assertManageableInProgram_differentProgram_forbidden()
    {
        $this->participant->program = $this->buildMockOfClass(Program::class);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertManageableInProgram();
        }, 'Forbidden', 'unmanaged participant');
    }
    public function test_assertManageableInProgram_sameProgram_void()
    {
        $this->assertManageableInProgram();
        $this->markAsSuccess();
    }
    
}

class TestableParticipant extends Participant
{

    public $program;
    public $id;
    public $status;
    public $programPrice;
    public $metricAssignment;
    public $evaluations;
    public $profiles;
    public $meetingInvitations;
    public $consultationRequests;
    public $consultationSessions;
    public $dedicatedMentors;
    //
    public $recordedEvents;

}
