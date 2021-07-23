<?php

namespace Tests\src\Personnel\Application\Service\Firm\Personnel\ProgramConsultant\DedicatedMentor;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\DedicatedMentor\DedicatedMentorRepository;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\DedicatedMentor\ExecuteTask;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ITaskExecutableByDedicatedMentor;
use Tests\TestBase;

class ExecuteTaskTest extends TestBase
{
    protected $dedicatedMentorRepository;
    protected $dedicatedMentor;
    protected $firmId = 'firm-id', $personnelId = 'personnel-id', $dedicatedMentorid = 'dedicated-mentor-id';
    protected $service;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dedicatedMentorRepository = $this->buildMockOfInterface(DedicatedMentorRepository::class);
        $this->dedicatedMentor = $this->buildMockOfClass(DedicatedMentor::class);
        $this->dedicatedMentorRepository->expects($this->any())
                ->method('aDedicatedMentorBelongsToPersonnel')
                ->with($this->firmId, $this->personnelId, $this->dedicatedMentorid)
                ->willReturn($this->dedicatedMentor);
        
        $this->service = new ExecuteTask($this->dedicatedMentorRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByDedicatedMentor::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->dedicatedMentorid, $this->task);
    }
    public function test_execute_dedicatedMentorExecuteTask()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('executeTask')
                ->with($this->task);
        $this->execute();
    }
    public function test_execute_upateRepository()
    {
        $this->dedicatedMentorRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
