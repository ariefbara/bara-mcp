<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultantRepository;
use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Tests\TestBase;

class ExecuteTaskTest extends TestBase
{
    protected $mentorRepository;
    protected $mentor;
    protected $firmId = 'firmId', $personnelId = 'personnelId', $mentorId = 'mentorId';
    protected $service;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->mentor = $this->buildMockOfClass(ProgramConsultant::class);
        $this->mentorRepository = $this->buildMockOfInterface(ProgramConsultantRepository::class);
        $this->mentorRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->personnelId, $this->mentorId)
                ->willReturn($this->mentor);
        $this->service = new ExecuteTask($this->mentorRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMentor::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->mentorId, $this->task);
    }
    public function test_execute_mentorExecuteTask()
    {
        $this->mentor->expects($this->once())
                ->method('executeTask')
                ->with($this->task);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->mentorRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
