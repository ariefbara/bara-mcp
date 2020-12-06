<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\ {
    Model\Firm,
    Model\Firm\Personnel,
    Model\Firm\Program,
    Model\Firm\Program\MeetingType\Meeting\Attendee,
    Model\Firm\Program\MeetingType\Meeting\Attendee\CoordinatorAttendee,
    Model\Firm\Program\MeetingType\MeetingData,
    Model\Firm\Program\Participant\EvaluationData,
    Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport,
    Service\MetricAssignmentDataProvider
};
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
    protected $meetingId = "meetingId", $meetingType, $meetingData;
    protected $activityParticipantType;
    protected $attendee;
    protected $metricAssignmentReport;
    protected $firm;
    protected $evaluationPlan, $evaluationData;

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
        
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        
        $this->activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        
        $this->metricAssignmentReport = $this->buildMockOfClass(MetricAssignmentReport::class);
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationData = $this->buildMockOfClass(EvaluationData::class);
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
        $errorDetail = "forbidden: only active coordinator can make this request";
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
    
    protected function executeInitiateMeeting()
    {
        $this->meetingType->expects($this->any())
                ->method("belongsToProgram")
                ->willReturn(true);
        return $this->coordinator->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
    public function test_initiateMeeting_returnMeetingTypeCreateMeetingResult()
    {
        $this->meetingType->expects($this->once())
                ->method("createMeeting")
                ->with($this->meetingId, $this->meetingData, $this->coordinator);
        $this->executeInitiateMeeting();
    }
    public function test_initiateMeeting_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $operation = function (){
            $this->executeInitiateMeeting();
        };
        $errorDetail = "forbidden: only active coordinator can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_initiateMeeting_meetingTypeFromDifferentProgram_forbidden()
    {
        $this->meetingType->expects($this->once())
                ->method("belongsToProgram")
                ->with($this->coordinator->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeInitiateMeeting();
        };
        $errorDetail = "forbidden: unable to manage asset of other program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_canInvolvedInProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->coordinator->canInvolvedInProgram($this->coordinator->program));
    }
    public function test_canInvolvedInProgram_inactiveConsultant_returnFalse()
    {
        $this->coordinator->active = false;
        $this->assertFalse($this->coordinator->canInvolvedInProgram($this->coordinator->program));
    }
    public function test_canInvolvedInProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->coordinator->canInvolvedInProgram($program));
    }
    
    public function test_roleCorrespondWith_returnAttendUserTypeIsCoordinatorResult()
    {
        $this->activityParticipantType->expects($this->once())
                ->method("isCoordinatorType");
        $this->coordinator->roleCorrespondWith($this->activityParticipantType);
    }
    
    protected function executeRegisterAsAttendeeCandidate()
    {
        $this->coordinator->registerAsAttendeeCandidate($this->attendee);
    }
    public function test_registerAsAttendeeCandidate_setCoordinatorAsAttendeeCandidate()
    {
        $this->attendee->expects($this->once())
                ->method("setCoordinatorAsAttendeeCandidate")
                ->with($this->coordinator);
        $this->executeRegisterAsAttendeeCandidate();
    }
    public function test_registerAsAttendeeCandidate_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $operation = function (){
            $this->executeRegisterAsAttendeeCandidate();
        };
        $errorDetail = "forbidden: can only invite active coordinator";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
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

}

class TestableCoordinator extends Coordinator
{
    public $program, $id, $personnel, $active;
    public $meetingInvitations;

}
