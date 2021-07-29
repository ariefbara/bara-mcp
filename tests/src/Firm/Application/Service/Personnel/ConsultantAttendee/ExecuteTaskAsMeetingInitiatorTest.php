<?php

namespace Firm\Application\Service\Personnel\ConsultantAttendee;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\Consultant\ConsultantAttendee;
use Tests\TestBase;

class ExecuteTaskAsMeetingInitiatorTest extends TestBase
{
    protected $consultantAttendeeRepository, $consultantAttendee, 
            $firmId = 'firm-id', $personnelId = 'personnel-id', $consultantAttendeeId = 'consultant-attendee-id';
    protected $service;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantAttendee = $this->buildMockOfClass(ConsultantAttendee::class);
        $this->consultantAttendeeRepository = $this->buildMockOfInterface(ConsultantAttendeeRepository::class);
        $this->consultantAttendeeRepository->expects($this->any())
                ->method('aConsultantAttendeeBelongsToPersonnel')
                ->with($this->firmId, $this->personnelId, $this->consultantAttendeeId)
                ->willReturn($this->consultantAttendee);
        
        $this->service = new ExecuteTaskAsMeetingInitiator($this->consultantAttendeeRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMeetingInitiator::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->consultantAttendeeId, $this->task);
    }
    public function test_execute_ConsultantAttendeeExecuteTask()
    {
        $this->consultantAttendee->expects($this->once())
                ->method('executeTaskAsMeetingInitiator')
                ->with($this->task);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultantAttendeeRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
