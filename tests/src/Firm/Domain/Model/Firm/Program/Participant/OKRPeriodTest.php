<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;
use Tests\TestBase;

class OKRPeriodTest extends TestBase
{
    protected $okrPeriod;
    protected $participant;
    protected $approvalStatus;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->okrPeriod = new TestableOKRPeriod();
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->okrPeriod->participant = $this->participant;
        
        $this->approvalStatus = $this->buildMockOfClass(OKRPeriodApprovalStatus::class);
        $this->okrPeriod->approvalStatus = $this->approvalStatus;
    }
    
    public function test_belongsToProgram_returnParticipantsBelongsToProgramResult()
    {
        $this->participant->expects($this->once())
                ->method('belongsToProgram');
        $this->okrPeriod->belongsToProgram($this->buildMockOfClass(Program::class));
    }
    
    protected function executeApprove()
    {
        $this->okrPeriod->approve();
    }
    public function test_approve_approveApprovalStatus()
    {
        $this->approvalStatus->expects($this->once())
                ->method('approve')
                ->willReturn($approvalStatus = $this->buildMockOfClass(OKRPeriodApprovalStatus::class));
        $this->executeApprove();
        $this->assertSame($approvalStatus, $this->okrPeriod->approvalStatus);
    }
    public function test_approve_alreadyCancelld_forbidden()
    {
        $this->okrPeriod->cancelled = true;
        $operation = function (){
            $this->executeApprove();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: okr period already cancelled');
    }
    
    protected function executeReject()
    {
        $this->okrPeriod->reject();
    }
    public function test_reject_rejectApprovalStatus()
    {
        $this->approvalStatus->expects($this->once())
                ->method('reject')
                ->willReturn($approvalStatus = $this->buildMockOfClass(OKRPeriodApprovalStatus::class));
        $this->executeReject();
        $this->assertSame($approvalStatus, $this->okrPeriod->approvalStatus);
    }
    public function test_reject_alreadyCancelld_forbidden()
    {
        $this->okrPeriod->cancelled = true;
        $operation = function (){
            $this->executeReject();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: okr period already cancelled');
    }
}

class TestableOKRPeriod extends OKRPeriod
{
    public $participant;
    public $id = 'id';
    public $approvalStatus;
    public $cancelled = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
