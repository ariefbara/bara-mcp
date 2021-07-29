<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\Application\Service\Firm\ManagerRepository;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingType\Meeting\Attendee;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InviteManagerToAttendMeetingTest extends TestBase
{
    protected $attendeeRepository, $attendee;
    protected $manager, $managerRepository;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $meetingId = "meetingId", $managerId = "managerId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method("anAttendeeBelongsToClientParticipantCorrespondWithMeeting")
                ->with($this->firmId, $this->clientId, $this->meetingId)
                ->willReturn($this->attendee);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->once())
                ->method("aManagerOfId")
                ->with($this->managerId)
                ->willReturn($this->manager);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new InviteManagerToAttendMeeting(
                $this->attendeeRepository, $this->managerRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->meetingId, $this->managerId);
    }
    public function test_execute_inviteManagerToAttendMeeting()
    {
        $this->attendee->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->manager);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->attendeeRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
    public function test_execute_dispatchAttendee()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->attendee);
        $this->execute();
    }
}
