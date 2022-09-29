<?php

namespace Query\Application\Service\Consultant;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;
use Tests\src\Query\Application\Service\Consultant\ConsultantServiceTestBase;

class ExecuteProgramTaskTest extends ConsultantServiceTestBase
{
    protected $service;
    protected $task, $payload = 'string represent task payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteProgramTask($this->consultantRepository);
        
        $this->task = $this->buildMockOfInterface(ProgramTaskExecutableByConsultant::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->consultantId, $this->task, $this->payload);
    }
    public function test_execute_consultantExecuteProgramTask()
    {
        $this->consultant->expects($this->once())
                ->method('executeProgramTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
}
