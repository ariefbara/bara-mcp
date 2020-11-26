<?php

namespace Firm\Domain\Model\Firm\Program\Participant\MetricAssignment;

use Firm\Domain\Model\Firm\ {
    Program,
    Program\Participant\MetricAssignment
};
use Tests\TestBase;

class MetricAssignmentReportTest extends TestBase
{
    protected $metricAssignmentReport;
    protected $metricAssignment;
    protected $program;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignmentReport = new TestableMetricAssignmentReport();
        
        $this->metricAssignment = $this->buildMockOfClass(MetricAssignment::class);
        $this->metricAssignmentReport->metricAssignment = $this->metricAssignment;
        
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    public function test_belongsToProgram_returnMetricAssignmentBelongsToProgramResult()
    {
        $this->metricAssignment->expects($this->once())
                ->method("belongsToProgram")
                ->with($this->program);
        $this->metricAssignmentReport->belongsToProgram($this->program);
                
    }
    
    public function test_approve_setApprovedFlagTrue()
    {
        $this->metricAssignmentReport->approve();
        $this->assertTrue($this->metricAssignmentReport->approved);
    }
}

class TestableMetricAssignmentReport extends MetricAssignmentReport
{
    public $metricAssignment;
    public $id;
    public $approved = false;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
