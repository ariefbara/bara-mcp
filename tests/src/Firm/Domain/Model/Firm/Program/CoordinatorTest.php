<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\ {
    Model\Firm\Personnel,
    Model\Firm\Program,
    Service\MetricAssignmentDataProvider
};
use Tests\TestBase;

class CoordinatorTest extends TestBase
{

    protected $program;
    protected $id = 'coordinator-id';
    protected $personnel;
    protected $coordinator;
    
    protected $participant;
    protected $metricAssignemtDataCollector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);

        $this->coordinator = new TestableCoordinator($this->program, 'id', $this->personnel);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->metricAssignemtDataCollector = $this->buildMockOfClass(MetricAssignmentDataProvider::class);
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

}

class TestableCoordinator extends Coordinator
{
    public $program, $id, $personnel, $removed;

}
