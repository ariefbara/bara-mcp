<?php

namespace Query\Application\Service\Consultant;

use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByConsultant;
use Tests\src\Query\Application\Service\Consultant\ConsultantServiceTestBase;

class ExecuteTaskInProgramTest extends ConsultantServiceTestBase
{

    protected $service;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteTaskInProgram($this->consultantRepository);

        $this->task = $this->buildMockOfInterface(ITaskInProgramExecutableByConsultant::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->consultantId, $this->task);
    }
    public function test_execute_consultantExecuteTask()
    {
        $this->consultant->expects($this->once())
                ->method('executeTaskInProgram')
                ->with($this->task);
        $this->execute();
    }

}
