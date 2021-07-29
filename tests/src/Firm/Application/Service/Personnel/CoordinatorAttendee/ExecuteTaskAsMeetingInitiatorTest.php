<?php

namespace Firm\Application\Service\Personnel\CoordinatorAttendee;

use Firm\Domain\Model\Firm\Program\Coordinator\CoordinatorAttendee;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Tests\TestBase;

class ExecuteTaskAsMeetingInitiatorTest extends TestBase
{
    protected $coordinatorAttendeeRepository, $coordinatorAttendee, 
            $firmId = 'firm-id', $personnelId = 'personnel-id', $coordinatorAttendeeId = 'coordinator-attendee-id';
    protected $service;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorAttendee = $this->buildMockOfClass(CoordinatorAttendee::class);
        $this->coordinatorAttendeeRepository = $this->buildMockOfInterface(CoordinatorAttendeeRepository::class);
        $this->coordinatorAttendeeRepository->expects($this->any())
                ->method('aCoordinatorAttendeeBelongsToPersonnel')
                ->with($this->firmId, $this->personnelId, $this->coordinatorAttendeeId)
                ->willReturn($this->coordinatorAttendee);
        
        $this->service = new ExecuteTaskAsMeetingInitiator($this->coordinatorAttendeeRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMeetingInitiator::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->coordinatorAttendeeId, $this->task);
    }
    public function test_execute_CoordinatorAttendeeExecuteTask()
    {
        $this->coordinatorAttendee->expects($this->once())
                ->method('executeTaskAsMeetingInitiator')
                ->with($this->task);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->coordinatorAttendeeRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
