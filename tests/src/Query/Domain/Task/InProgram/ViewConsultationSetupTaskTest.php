<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ConsultationSetup;
use Query\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Tests\src\Query\Domain\Task\InProgram\TaskInProgramTestBase;

class ViewConsultationSetupTaskTest extends TaskInProgramTestBase
{

    protected $consultationSetupRepository;
    protected $consultationSetup;
    protected $consultationSetupId = 'consultationSetupId';
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);
        $this->consultationSetupRepository->expects($this->any())
                ->method('aConsultationSetupInProgram')
                ->with($this->programId, $this->consultationSetupId)
                ->willReturn($this->consultationSetup);
        
        $this->task = new ViewConsultationSetupTask($this->consultationSetupRepository, $this->consultationSetupId);
    }
    
    protected function execute()
    {
        $this->task->executeTaskInProgram($this->program);
    }
    public function test_execute_setConsultationSetupAsResult()
    {
        $this->execute();
        $this->assertEquals($this->consultationSetup, $this->task->result);
    }

}
