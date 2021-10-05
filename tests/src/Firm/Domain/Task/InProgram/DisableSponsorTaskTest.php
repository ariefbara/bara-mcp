<?php

namespace Firm\Domain\Task\InProgram;

use Tests\src\Firm\Domain\Task\InProgram\SponsorTaskTestBase;

class DisableSponsorTaskTest extends SponsorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new DisableSponsorTask($this->sponsorRepository, $this->sponsorId);
    }
    
    protected function executeInProgram()
    {
        $this->task->executeInProgram($this->program);
    }
    public function test_executeInProgram_disableSponsor()
    {
        $this->sponsor->expects($this->once())
                ->method('disable');
        $this->executeInProgram();
    }
    public function test_executeInProgram_assertSponsorManageableInProgram()
    {
        $this->sponsor->expects($this->once())
                ->method('assertManageableInProgram')
                ->with($this->program);
        $this->executeInProgram();
    }
}
