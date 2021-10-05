<?php

use Doctrine\ORM\Tools\Pagination\Paginator;
use Query\Domain\Model\Firm\Program\Sponsor;
use Query\Domain\Task\Dependency\Firm\Program\SponsorRepository;
use Query\Domain\Task\InProgram\ViewAllSponsorsPayload;
use Query\Domain\Task\InProgram\ViewAllSponsorsTask;
use Tests\src\Query\Domain\Task\InProgram\TaskInProgramTestBase;

class ViewAllSponsorsTaskTest extends TaskInProgramTestBase
{
    protected $sponsorRepository;
    protected $payload, $page = 2, $pageSize = 10, $activeStatus = true;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->sponsorRepository = $this->buildMockOfInterface(SponsorRepository::class);
        $this->payload = new ViewAllSponsorsPayload($this->page, $this->pageSize, $this->activeStatus);
        $this->task = new ViewAllSponsorsTask($this->sponsorRepository, $this->payload);
        
        $this->sponsor = $this->buildMockOfClass(Sponsor::class);
        
    }
    
    protected function executeInProgram()
    {
        $this->task->executeInProgram($this->program);
    }
    public function test_executeInProgram_setRepositoryQueryAsResult()
    {
        $this->sponsorRepository->expects($this->once())
                ->method('allSponsorsInProgram')
                ->with($this->program, $this->page, $this->pageSize, $this->activeStatus)
                ->willReturn($results = Paginator::class);
        $this->task->executeInProgram($this->program);
        $this->assertEquals($results, $this->task->result);
    }
}
