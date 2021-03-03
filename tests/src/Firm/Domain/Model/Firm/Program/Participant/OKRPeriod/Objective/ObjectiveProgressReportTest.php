<?php

namespace Firm\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;
use Tests\TestBase;

class ObjectiveProgressReportTest extends TestBase
{
    protected $objectiveProgressReport;
    protected $objective;
    protected $approvalStatus;
    protected $mutatedApprovalStatus;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->objectiveProgressReport = new TestableObjectiveProgressReport();
        
        $this->objective = $this->buildMockOfClass(Objective::class);
        $this->objectiveProgressReport->objective = $this->objective;
        
        $this->approvalStatus = $this->buildMockOfClass(OKRPeriodApprovalStatus::class);
        $this->mutatedApprovalStatus = $this->buildMockOfClass(OKRPeriodApprovalStatus::class);
        $this->objectiveProgressReport->approvalStatus = $this->approvalStatus;
    }
    protected function assertCancelled(callable $operation): void
    {
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: progress report submission already cancelled');
    }
    
    public function test_belongsToProgram_returnObjectivesBelongsToProgramResult()
    {
        $this->objective->expects($this->once())
                ->method('belongsToProgram')
                ->with($program = $this->buildMockOfClass(Program::class));
        $this->objectiveProgressReport->belongsToProgram($program);
    }
    
    protected function executeApprove()
    {
        $this->objectiveProgressReport->approve();
    }
    public function test_approve_executeApprovalStatusesApprove()
    {
        $this->approvalStatus->expects($this->once())
                ->method('approve')
                ->willReturn($this->mutatedApprovalStatus);
        $this->executeApprove();
        $this->assertSame($this->mutatedApprovalStatus, $this->objectiveProgressReport->approvalStatus);
    }
    public function test_approve_cancelled_forbidden()
    {
        $this->objectiveProgressReport->cancelled = true;
        $this->assertCancelled(function (){
            $this->executeApprove();
        });
    }
    
    protected function executeReject()
    {
        $this->objectiveProgressReport->reject();
    }
    public function test_reject_executeApprovalStatusesReject()
    {
        $this->approvalStatus->expects($this->once())
                ->method('reject')
                ->willReturn($this->mutatedApprovalStatus);
        $this->executeReject();
        $this->assertSame($this->mutatedApprovalStatus, $this->objectiveProgressReport->approvalStatus);
    }
    public function test_reject_cancelled_forbidden()
    {
        $this->objectiveProgressReport->cancelled = true;
        $this->assertCancelled(function (){
            $this->executeReject();
        });
    }
}

class TestableObjectiveProgressReport extends ObjectiveProgressReport
{
    public $objective;
    public $id = 'id';
    public $approvalStatus;
    public $cancelled = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
