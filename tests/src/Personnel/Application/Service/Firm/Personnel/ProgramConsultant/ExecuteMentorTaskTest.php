<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultantRepository;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Mentor\MentorTask;
use Tests\TestBase;

class ExecuteMentorTaskTest extends TestBase
{

    protected $mentorRepository, $mentor, $firmId = 'firmId', $personnelId = 'personnelId', $mentorId = 'mentorId';
    protected $service;
    protected $mentorTask, $payload = 'string represent task payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->mentorRepository = $this->buildMockOfInterface(ProgramConsultantRepository::class);
        $this->mentor = $this->buildMockOfClass(ProgramConsultant::class);
        $this->mentorRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->personnelId, $this->mentorId)
                ->willReturn($this->mentor);
        
        $this->service = new ExecuteMentorTask($this->mentorRepository);
        
        $this->mentorTask = $this->buildMockOfInterface(MentorTask::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->mentorId, $this->mentorTask, $this->payload);
    }
    public function test_execute_mentorExecuteMentorTask()
    {
        $this->mentor->expects($this->once())
                ->method('executeMentorTask')
                ->with($this->mentorTask, $this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->mentorRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

}
