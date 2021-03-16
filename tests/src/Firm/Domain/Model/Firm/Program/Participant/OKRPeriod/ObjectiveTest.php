<?php

namespace Firm\Domain\Model\Firm\Program\Participant\OKRPeriod;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Tests\TestBase;

class ObjectiveTest extends TestBase
{
    protected $objective;
    protected $okrPeriod;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->objective = new TestableObjective();
        $this->okrPeriod = $this->buildMockOfClass(OKRPeriod::class);
        $this->objective->okrPeriod = $this->okrPeriod;
    }
    
    public function test_belongsToProgram_returnOKRPeriodBelongsToProgramResult()
    {
        $this->okrPeriod->expects($this->once())
                ->method('belongsToProgram')
                ->with($program = $this->buildMockOfClass(Program::class));
        $this->objective->belongsToProgram($program);
    }
}

class TestableObjective extends Objective
{
    public $okrPeriod;
    public $id = 'id';
    
    function __construct()
    {
        parent::__construct();
    }
}
