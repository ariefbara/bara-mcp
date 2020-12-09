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
    protected $note = "new note";
    
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
    
    protected function executeApprove()
    {
        $this->metricAssignmentReport->approve();
    }
    public function test_approve_setApprovedFlagTrue()
    {
        $this->executeApprove();
        $this->assertTrue($this->metricAssignmentReport->approved);
    }
    public function test_approve_alreadyDecided_forbidden()
    {
        $this->metricAssignmentReport->approved = false;
        $operation = function (){
            $this->executeApprove();
        };
        $errorDetail = "forbidden: unable to alter approval decision";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeReject()
    {
        $this->metricAssignmentReport->reject($this->note);
    }
    public function test_reject_setApprovedFalseAndNote()
    {
        $this->executeReject();
        $this->assertFalse($this->metricAssignmentReport->approved);
        $this->assertEquals($this->note, $this->metricAssignmentReport->note);
    }
    public function test_reject_alreadyProcessed_forbidden()
    {
        $this->metricAssignmentReport->approved = false;
        $operation = function (){
            $this->executeReject();
        };
        $errorDetail = "forbidden: unable to alter approval decision";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}

class TestableMetricAssignmentReport extends MetricAssignmentReport
{
    public $metricAssignment;
    public $id;
    public $approved = null;
    public $note;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
