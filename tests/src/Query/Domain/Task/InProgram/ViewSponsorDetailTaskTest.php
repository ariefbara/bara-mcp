<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\Sponsor;
use Query\Domain\Task\Dependency\Firm\Program\SponsorRepository;
use Tests\src\Query\Domain\Task\InProgram\TaskInProgramTestBase;

class ViewSponsorDetailTaskTest extends TaskInProgramTestBase
{
    protected $sponsorRepository;
    protected $sponsor;
    protected $sponsorId = "sponsor-id";
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->sponsor = $this->buildMockOfClass(Sponsor::class);
        $this->sponsorRepository = $this->buildMockOfInterface(SponsorRepository::class);
        $this->sponsorRepository->expects($this->any())
                ->method('aSponsorInProgram')
                ->with($this->program, $this->sponsorId)
                ->willReturn($this->sponsor);
        $this->task = new ViewSponsorDetailTask($this->sponsorRepository, $this->sponsorId);
    }
    protected function executeInProgram()
    {
        $this->task->executeInProgram($this->program);
    }
    public function test_executeInProgram_setSponsorAsResult()
    {
        $this->executeInProgram();
        $this->assertEquals($this->sponsor, $this->task->result);
    }
}
