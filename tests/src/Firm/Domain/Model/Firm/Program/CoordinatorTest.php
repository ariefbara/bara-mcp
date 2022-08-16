<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Personnel;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Firm\Domain\Model\Firm\Program\Coordinator\CoordinatorAttendee;
use Firm\Domain\Model\Firm\Program\Participant\EvaluationData;
use Firm\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Firm\Domain\Service\MetricAssignmentDataProvider;
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class CoordinatorTest extends TestBase
{

    protected $program;
    protected $id = 'coordinator-id';
    protected $personnel;
    protected $coordinator;
    protected $coordinatorAttendee;
    
    protected $participant;
    protected $metricAssignemtDataCollector;
    protected $activityParticipantType;
    protected $attendee;
    protected $metricAssignmentReport, $note = "new note";
    protected $firm;
    protected $evaluationPlan, $evaluationData;
    protected $consultationSession, $media = "new media", $address = "new Address";
    
    protected $meetingId = "meetingId", $meetingType, $meetingData;
    protected $meeting;
    //
    protected $programTask, $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnel->expects($this->any())->method("isActive")->willReturn(true);

        $this->coordinator = new TestableCoordinator($this->program, 'id', $this->personnel);
        
        $this->coordinator->meetingInvitations = new ArrayCollection();
        $this->coordinatorAttendee = $this->buildMockOfClass(CoordinatorAttendee::class);
        $this->coordinator->meetingInvitations->add($this->coordinatorAttendee);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->metricAssignemtDataCollector = $this->buildMockOfClass(MetricAssignmentDataProvider::class);
        
        
        $this->activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        
        $this->metricAssignmentReport = $this->buildMockOfClass(MetricAssignmentReport::class);
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationData = $this->buildMockOfClass(EvaluationData::class);
        
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        //
        $this->programTask = $this->buildMockOfInterface(TaskInProgramExecutableByCoordinator::class);
    }
    
    protected function setAssetBelongsToProgram($asset)
    {
        $asset->expects($this->any())
                ->method("belongsToProgram")
                ->with($this->program)
                ->willReturn(true);
    }
    protected function setAssetNotBelongsToProgram($asset)
    {
        $asset->expects($this->once())
                ->method("belongsToProgram")
                ->with($this->program)
                ->willReturn(false);
    }
    protected function assertAssetNotBelongsToProgramForbiddenError(callable $operation)
    {
        $errorDetail = "forbidden: unable to manage asset of other program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    protected function assertInactiveCoordinatorForbiddenError(callable $operation)
    {
        $errorDetail = "forbidden: inactive coordinator";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeConstruct()
    {
        $this->personnel->expects($this->any())->method("isActive")->willReturn(true);
        return new TestableCoordinator($this->program, $this->id, $this->personnel);
    }
    public function test_construct_setProperties()
    {
        $coordinator = $this->executeConstruct();
        $this->assertEquals($this->program, $coordinator->program);
        $this->assertEquals($this->id, $coordinator->id);
        $this->assertEquals($this->personnel, $coordinator->personnel);
        $this->assertTrue($coordinator->active);
    }
    public function test_inactivePersonnel_forbidden()
    {
        $operation = function (){
            $personnel = $this->buildMockOfClass(Personnel::class);
            $personnel->expects($this->any())->method("isActive")->willReturn(false);
            new TestableCoordinator($this->program, $this->id, $personnel);
        };
        $errorDetail = "forbidden: only active personnel can be assigned as coordinator";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
        
    }

    protected function executeDisable()
    {
        $this->coordinator->disable();
    }
    public function test_disable_disableCoordinator()
    {
        $this->executeDisable();
        $this->assertFalse($this->coordinator->active);
    }
    public function test_disable_disableAllValidInvitation()
    {
        $this->coordinatorAttendee->expects($this->once())
                ->method("disableValidInvitation");
        $this->executeDisable();
    }

    public function test_reassign_setRemovedFlagFalse()
    {
        $this->coordinator->active = false;
        $this->coordinator->enable();
        $this->assertTrue($this->coordinator->active);
    }
    
    protected function executeAssignMetricToParticipant()
    {
        $this->setAssetBelongsToProgram($this->participant);
        $this->coordinator->assignMetricsToParticipant($this->participant, $this->metricAssignemtDataCollector);
    }
    public function test_assignMetricToParticipant_assigneMetricToParticipant()
    {
        $this->participant->expects($this->once())
                ->method('assignMetrics')
                ->with($this->metricAssignemtDataCollector);
        $this->executeAssignMetricToParticipant();
    }
    public function test_assignMetricToParticipant_inactiveCoordinator_forbiddenError()
    {
        $this->coordinator->active = false;
        $this->assertInactiveCoordinatorForbiddenError(function () {
            $this->executeAssignMetricToParticipant();
        });
    }
    public function test_assignMetricToParticipant_participantNotIsSameProgram_forbiddenError()
    {
        $this->setAssetNotBelongsToProgram($this->participant);
        $this->assertAssetNotBelongsToProgramForbiddenError(function (){
            $this->executeAssignMetricToParticipant();
        });
    }
    
    protected function executeApproveMetricAssignmentReport()
    {
        $this->setAssetBelongsToProgram($this->metricAssignmentReport);
        $this->coordinator->approveMetricAssignmentReport($this->metricAssignmentReport);
    }
    public function test_approveMetricAssignmentReport_approveReport()
    {
        $this->metricAssignmentReport->expects($this->once())
                ->method("approve");
        $this->executeApproveMetricAssignmentReport();
    }
    public function test_approveMetricAssignmentReport_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertInactiveCoordinatorForbiddenError(function (){
            $this->executeApproveMetricAssignmentReport();
        });
    }
    public function test_approveMetricAssignmentReport_metricAssignmentReportDoesntBelongsToProgram_forbidden()
    {
        $this->setAssetNotBelongsToProgram($this->metricAssignmentReport);
        $this->assertAssetNotBelongsToProgramForbiddenError(function (){
            $this->executeApproveMetricAssignmentReport();
        });
    }
    
    protected function executeRejectMetricAssignmentReport()
    {
        $this->setAssetBelongsToProgram($this->metricAssignmentReport);
        $this->coordinator->rejectMetricAssignmentReport($this->metricAssignmentReport, $this->note);
    }
    public function test_rejectMetricAssignmentReport_rejectReport()
    {
        $this->metricAssignmentReport->expects($this->once())
                ->method("reject")
                ->with($this->note);
        $this->executeRejectMetricAssignmentReport();
    }
    public function test_rejectMetricAssignmentReport_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertInactiveCoordinatorForbiddenError(function (){
            $this->executeRejectMetricAssignmentReport();
        });
    }
    public function test_rejectMetricAssignmentReport_metricAssignmentReportDoesntBelongsToProgram_forbidden()
    {
        $this->setAssetNotBelongsToProgram($this->metricAssignmentReport);
        $this->assertAssetNotBelongsToProgramForbiddenError(function (){
            $this->executeRejectMetricAssignmentReport();
        });
    }
    
    public function test_belongsToFirm_returnProgramsBelongsToFirmResult()
    {
        $this->program->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm);
        $this->coordinator->belongsToFirm($this->firm);
    }
    
    protected function executeEvaluateParticipant()
    {
        $this->setAssetBelongsToProgram($this->participant);
        $this->setAssetBelongsToProgram($this->evaluationPlan);
        $this->coordinator->evaluateParticipant($this->participant, $this->evaluationPlan, $this->evaluationData);
    }
    public function test_evaluateParticipant_participantReceiveFailEvaluation()
    {
        $this->participant->expects($this->once())
                ->method("receiveEvaluation")
                ->with($this->evaluationPlan, $this->evaluationData, $this->coordinator);
        $this->executeEvaluateParticipant();
    }
    public function test_evaluateParticipant_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertInactiveCoordinatorForbiddenError(function (){
            $this->executeEvaluateParticipant();
        });
    }
    public function test_evaluateParticipant_participantFromDifferentProgram_forbidden()
    {
        $this->setAssetNotBelongsToProgram($this->participant);
        $this->assertAssetNotBelongsToProgramForbiddenError(function (){
            $this->executeEvaluateParticipant();
        });
    }
    public function test_evaluateParticipant_evaluationPlanFromDifferentProgram_forbidden()
    {
        $this->setAssetNotBelongsToProgram($this->evaluationPlan);
        $this->assertAssetNotBelongsToProgramForbiddenError(function (){
            $this->executeEvaluateParticipant();
        });
    }
    
    protected function executeQualifyParticipant()
    {
        $this->setAssetBelongsToProgram($this->participant);
        $this->coordinator->qualifyParticipant($this->participant);
    }
    public function test_qualifyParticipant_qualifyParticipant()
    {
        $this->participant->expects($this->once())->method("qualify");
        $this->executeQualifyParticipant();
    }
    public function test_qualifyParticipant_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertInactiveCoordinatorForbiddenError(function (){
            $this->executeQualifyParticipant();
        });
    }
    public function test_qualifyParticipant_unmanageableParticipant_forbidden()
    {
        $this->setAssetNotBelongsToProgram($this->participant);
        $this->assertAssetNotBelongsToProgramForbiddenError(function (){
            $this->executeQualifyParticipant();
        });
    }
    
    protected function executeChangeConsultationSessionChannel()
    {
        $this->setAssetBelongsToProgram($this->consultationSession);
        $this->coordinator->changeConsultationSessionChannel($this->consultationSession, $this->media, $this->address);
    }
    public function test_changeConsultationSessionChannel_changeConsultationSessionChannel()
    {
        $this->consultationSession->expects($this->once())
                ->method("changeChannel")
                ->with($this->media, $this->address);
        $this->executeChangeConsultationSessionChannel();
    }
    public function test_changeConsultationSessionChannel_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertInactiveCoordinatorForbiddenError(function (){
            $this->executeChangeConsultationSessionChannel();
        });
    }
    public function test_changeConsultationSessionChannel_unmanageableConsultationSession_forbidden()
    {
        $this->setAssetNotBelongsToProgram($this->consultationSession);
        $this->assertAssetNotBelongsToProgramForbiddenError(function (){
            $this->executeChangeConsultationSessionChannel();
        });
    }
    
    protected function executeInitiateMeeting()
    {
        return $this->coordinator->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
    public function test_initiateMeeting_returnMeetingCreatedInActivityType()
    {
        $this->meetingType->expects($this->once())
                ->method('createMeeting')
                ->with($this->meetingId, $this->meetingData)
                ->willReturn($meeting = $this->buildMockOfClass(Meeting::class));
        $this->assertEquals($meeting, $this->executeInitiateMeeting());
    }
    public function test_initiateMeeting_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertInactiveCoordinatorForbiddenError(function (){
            $this->executeInitiateMeeting();
        });
    }
    public function test_initiateMeeting_assertMeetingTypeUsableInProgram()
    {
        $this->meetingType->expects($this->once())
                ->method('assertUsableInProgram')
                ->with($this->program);
        $this->executeInitiateMeeting();
    }
    public function test_initiateMeeting_aggregateCoordinatorAttendeeToMeetingInvitationCollection()
    {
        $this->executeInitiateMeeting();
        $this->assertEquals(2, $this->coordinator->meetingInvitations->count());
        $this->assertInstanceOf(CoordinatorAttendee::class, $this->coordinator->meetingInvitations->last());
    }
    
    protected function executeInviteToMeeting()
    {
        $this->coordinator->inviteToMeeting($this->meeting);
    }
    public function test_inviteToMeeting_addNewCoordinatorAttendeeToMeetingInvitationCollection()
    {
        $this->executeInviteToMeeting();
        $this->assertEquals(2, $this->coordinator->meetingInvitations->count());
        $this->assertInstanceOf(CoordinatorAttendee::class, $this->coordinator->meetingInvitations->last());
    }
    public function test_inviteToMeeting_hasActiveInvitationToSameMeeting_void()
    {
        $this->coordinatorAttendee->expects($this->once())
                ->method('isActiveAttendeeOfMeeting')
                ->with($this->meeting)
                ->willReturn(true);
        $this->executeInviteToMeeting();
        $this->assertEquals(1, $this->coordinator->meetingInvitations->count());
    }
    public function test_inviteToMeeting_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertInactiveCoordinatorForbiddenError(function (){
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
    
    //
    protected function executeProgramTask()
    {
        $this->coordinator->executeProgramTask($this->programTask, $this->payload);
    }
    public function test_executeProgramTask_executeProgramTask()
    {
        $this->programTask->expects($this->once())
                ->method('execute')
                ->with($this->program, $this->payload);
        $this->executeProgramTask();
    }
    public function test_executeProgramTask_inactive_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertRegularExceptionThrowed(function () {
            $this->executeProgramTask();
        }, 'Forbidden', 'only active coordinator can make this request');
    }
}

class TestableCoordinator extends Coordinator
{
    public $program, $id, $personnel, $active;
    public $meetingInvitations;

}
