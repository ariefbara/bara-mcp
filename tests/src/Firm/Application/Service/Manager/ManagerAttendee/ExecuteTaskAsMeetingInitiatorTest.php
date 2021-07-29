<?php

namespace Firm\Application\Service\Manager\ManagerAttendee;

use Firm\Domain\Model\Firm\Manager\ManagerAttendee;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Tests\TestBase;

class ExecuteTaskAsMeetingInitiatorTest extends TestBase
{
    protected $managerAttendeeRepository, $managerAttendee, 
            $firmId = 'firm-id', $managerId = 'manager-id', $managerAttendeeId = 'manager-attendee-id';
    protected $service;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->managerAttendee = $this->buildMockOfClass(ManagerAttendee::class);
        $this->managerAttendeeRepository = $this->buildMockOfInterface(ManagerAttendeeRepository::class);
        $this->managerAttendeeRepository->expects($this->any())
                ->method('aManagerAttendeeBelongsToManager')
                ->with($this->firmId, $this->managerId, $this->managerAttendeeId)
                ->willReturn($this->managerAttendee);
        
        $this->service = new ExecuteTaskAsMeetingInitiator($this->managerAttendeeRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMeetingInitiator::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->managerAttendeeId, $this->task);
    }
    public function test_execute_ManagerAttendeeExecuteTask()
    {
        $this->managerAttendee->expects($this->once())
                ->method('executeTaskAsMeetingInitiator')
                ->with($this->task);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->managerAttendeeRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
