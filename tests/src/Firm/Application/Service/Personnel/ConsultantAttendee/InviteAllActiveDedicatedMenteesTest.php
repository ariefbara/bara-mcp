<?php

namespace Firm\Application\Service\Personnel\ConsultantAttendee;

use Firm\Domain\Model\Firm\Program\Consultant\ConsultantAttendee;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InviteAllActiveDedicatedMenteesTest extends TestBase
{
    protected $consultantAttendeeRepository, $consultantAttendee,
            $firmId = 'firm-id', $personnelId = 'personnel-id', $meetingId = 'meeting-id';
    protected $dispatcher;
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantAttendee = $this->buildMockOfClass(ConsultantAttendee::class);
        $this->consultantAttendeeRepository = $this->buildMockOfInterface(ConsultantAttendeeRepository::class);
        $this->consultantAttendeeRepository->expects($this->any())
                ->method('aConsultantAttendeeBelongsToPersonnel')
                ->with($this->firmId, $this->personnelId, $this->meetingId)
                ->willReturn($this->consultantAttendee);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new InviteAllActiveDedicatedMentees($this->consultantAttendeeRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->meetingId);
    }
    public function test_execute_consultantAttendeeInviteAllDedicatedMentees()
    {
        $this->consultantAttendee->expects($this->once())
                ->method('inviteAllActiveDedicatedMentees');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultantAttendeeRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
    public function test_execute_dispatchConsultantAttendee()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->consultantAttendee);
        $this->execute();
    }
}
