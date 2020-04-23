<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Mentor;
use Tests\TestBase;

class MentorRemoveTest extends TestBase
{
    protected $service;
    protected $mentorRepository, $mentor, $programCompositionId, $mentorId = 'mentor-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programCompositionId = $this->buildMockOfClass(ProgramCompositionId::class);
        
        $this->mentor = $this->buildMockOfClass(Mentor::class);
        $this->mentorRepository = $this->buildMockOfInterface(MentorRepository::class);
        $this->mentorRepository->expects($this->any())
            ->method('ofId')
            ->with($this->programCompositionId, $this->mentorId)
            ->willReturn($this->mentor);
        
        $this->service = new MentorRemove($this->mentorRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->programCompositionId, $this->mentorId);
    }
    
    public function test_execute_removeMentor()
    {
        $this->mentor->expects($this->once())
            ->method('remove');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->mentorRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
}
