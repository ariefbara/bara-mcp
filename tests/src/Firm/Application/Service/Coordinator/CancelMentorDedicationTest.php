<?php

namespace Firm\Application\Service\Coordinator;

use Tests\src\Firm\Application\Service\Coordinator\DedicatedMentorTestBase;

class CancelMentorDedicationTest extends DedicatedMentorTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CancelMentorDedication($this->coordinatorRepository, $this->dedicatedMentorRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->dedicatedMentorId);
    }
    public function test_execute_coordinatorCancelMentorDedication()
    {
        $this->coordinator->expects($this->once())
                ->method('cancelMentorDedication')
                ->with($this->dedicatedMentor);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->coordinatorRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
