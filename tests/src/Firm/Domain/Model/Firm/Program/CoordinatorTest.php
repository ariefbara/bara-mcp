<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\ {
    Model\Firm\Personnel,
    Model\Firm\Program,
    Model\Firm\Program\MeetingType\Meeting\Attendee,
    Model\Firm\Program\MeetingType\MeetingData,
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
    
    protected $participant;
    protected $metricAssignemtDataCollector;
    protected $meetingId = "meetingId", $meetingType, $meetingData;
    protected $activityParticipantType;
    protected $attendee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);

        $this->coordinator = new TestableCoordinator($this->program, 'id', $this->personnel);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->metricAssignemtDataCollector = $this->buildMockOfClass(MetricAssignmentDataProvider::class);
        
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        
        $this->activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        
        $this->attendee = $this->buildMockOfClass(Attendee::class);
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

    public function test_construct_setProperties()
    {
        $coordinator = new TestableCoordinator($this->program, $this->id, $this->personnel);
        $this->assertEquals($this->program, $coordinator->program);
        $this->assertEquals($this->id, $coordinator->id);
        $this->assertEquals($this->personnel, $coordinator->personnel);
        $this->assertFalse($coordinator->removed);
    }

    public function test_remove_setRemovedFlagTrue()
    {
        $this->coordinator->remove();
        $this->assertTrue($this->coordinator->removed);
    }

    public function test_reassign_setRemovedFlagFalse()
    {
        $this->coordinator->removed = true;
        $this->coordinator->reassign();
        $this->assertFalse($this->coordinator->removed);
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
        $this->coordinator->removed = true;
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
        $this->coordinator->removed = true;
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
    
    public function test_registerAsAttendeeCandidate_setCoordinatorAsAttendeeCandidate()
    {
        $this->attendee->expects($this->once())
                ->method("setCoordinatorAsAttendeeCandidate")
                ->with($this->coordinator);
        $this->coordinator->registerAsAttendeeCandidate($this->attendee);
    }

}

class TestableCoordinator extends Coordinator
{
    public $program, $id, $personnel, $removed;

}
