<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Tests\src\Query\Domain\Task\InProgram\TaskInProgramTestBase;

class ViewAllConsultationSetupTaskTest extends TaskInProgramTestBase
{
    protected $consultationSetupRepository;
    protected $payload, $page = 2, $pageSize = 10;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);
        $this->payload = new ViewAllConsultationSetupPayload($this->page, $this->pageSize);
        $this->task = new ViewAllConsultationSetupTask($this->consultationSetupRepository, $this->payload);
    }
    
    protected function executeTaskInProgram()
    {
        $this->task->executeTaskInProgram($this->program);
    }
    public function test_executeTaskInProgram_setRepositoryAllQueryAsResult()
    {
        $this->consultationSetupRepository->expects($this->once())
                ->method('allConsultationSetupsInProgram')
                ->with($this->programId, $this->page, $this->pageSize)
                ->willReturn($result = 'string represent query result');
        $this->executeTaskInProgram();
        $this->assertEquals($result, $this->task->result);
    }
    
}
