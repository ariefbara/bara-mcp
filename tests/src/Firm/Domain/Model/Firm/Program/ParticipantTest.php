<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Firm\Domain\ {
    Model\Firm\Program,
    Model\Firm\Program\MeetingType\Meeting\Attendee,
    Model\Firm\Program\Participant\MetricAssignment,
    Service\MetricAssignmentDataProvider
};
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ParticipantTest extends TestBase
{

    protected $program;
    protected $participant;
    protected $inactiveParticipant;
    protected $clientParticipant;
    protected $userParticipant;
    protected $teamParticipant;
    protected $id = 'newParticipantId', $userId = 'userId', $clientId = 'clientId', $teamId = "teamId";
    protected $registrant;
    protected $metricAssignment;
    protected $metricAssignmentDataProvider;
    protected $metric;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);

        $this->participant = new TestableParticipant($this->program, 'id');
        $this->inactiveParticipant = new TestableParticipant($this->program, 'id');
        $this->inactiveParticipant->active = false;
        $this->inactiveParticipant->note = 'booted';

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
    }

    public function test_participantForUser_setProperties()
    {
        $participant = TestableParticipant::participantForUser($this->program, $this->id, $this->userId);
        $this->assertEquals($this->program, $participant->program);
        $this->assertEquals($this->id, $participant->id);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $participant->enrolledTime);
        $this->assertTrue($participant->active);
        $this->assertNull($participant->note);

        $userParticipant = new UserParticipant($participant, $this->id, $this->userId);
        $this->assertEquals($userParticipant, $participant->userParticipant);
        $this->assertNull($participant->clientParticipant);
    }

    public function test_participantForClient_setProperties()
    {
        $participant = TestableParticipant::participantForClient($this->program, $this->id, $this->clientId);
        $this->assertEquals($this->program, $participant->program);
        $this->assertEquals($this->id, $participant->id);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $participant->enrolledTime);
        $this->assertTrue($participant->active);
        $this->assertNull($participant->note);

        $clientParticipant = new ClientParticipant($participant, $this->id, $this->clientId);
        $this->assertEquals($clientParticipant, $participant->clientParticipant);
        $this->assertNull($participant->userParticipant);
    }

    public function test_participantForTeam_setProperties()
    {
        $participant = TestableParticipant::participantForTeam($this->program, $this->id, $this->teamId);
        $this->assertEquals($this->program, $participant->program);
        $this->assertEquals($this->id, $participant->id);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $participant->enrolledTime);
        $this->assertTrue($participant->active);
        $this->assertNull($participant->note);

        $teamParticipant = new TeamParticipant($participant, $this->id, $this->teamId);
        $this->assertEquals($teamParticipant, $participant->teamParticipant);
        $this->assertNull($participant->userParticipant);
        $this->assertNull($participant->clientParticipant);
    }

    public function test_belongsToProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->participant->belongsToProgram($this->participant->program));
    }

    public function test_belongsTo_differentprogram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->participant->belongsToProgram($program));
    }

    protected function executeBootout()
    {
        $this->participant->bootout();
    }

    public function test_bootout_setActiveFlagFalseAndNoteBooted()
    {
        $this->executeBootout();
        $this->assertFalse($this->participant->active);
        $this->assertEquals('booted', $this->participant->note);
    }

    public function test_bootout_alreadyInactive_forbiddenError()
    {
        $this->participant->active = false;
        $operation = function () {
            $this->executeBootout();
        };
        $errorDetail = 'forbidden: participant already inactive';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
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
    
    public function test_canInvolvedInProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->participant->canInvolvedInProgram($this->participant->program));
    }
    public function test_canInvolvedInProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->participant->canInvolvedInProgram($program));
    }
    
    public function test_roleCorrespondWith_returnActivityParticipantTypeIsParticipantResult()
    {
        $activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        $activityParticipantType->expects($this->once())
                ->method("isParticipantType");
        $this->participant->roleCorrespondWith($activityParticipantType);
    }
    
    public function test_registerAsAttendeeCandidate_setParticipantAsAttendeeCandidate()
    {
        $attendee = $this->buildMockOfClass(Attendee::class);
        $attendee->expects($this->once())
                ->method("setParticipantAsAttendeeCandidate")
                ->with($this->participant);
        $this->participant->registerAsAttendeeCandidate($attendee);
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

    public function __construct(Program $program, string $id)
    {
        parent::__construct($program, $id);
    }

}
