<?php

use Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant\ExecuteTaskAsMemberOfTeamParticipantMeetingInitiator;
use Firm\Application\Service\Firm\Program\Participant\ParticipantAttendeeRepository;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Tests\src\Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant\AsProgramParticipantTestBase;

class ExecuteTaskAsMemberOfTeamParticipantMeetingInitiatorTest extends AsProgramParticipantTestBase
{
    protected $participantAttendeeRepository, $participantAttendee, $attendeeId = 'attendee-id';
    protected $service;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantAttendee = $this->buildMockOfClass(ParticipantAttendee::class);
        $this->participantAttendeeRepository = $this->buildMockOfInterface(ParticipantAttendeeRepository::class);
        $this->participantAttendeeRepository->expects($this->any())
                ->method('ofId')
                ->with($this->attendeeId)
                ->willReturn($this->participantAttendee);
        
        $this->service = new ExecuteTaskAsMemberOfTeamParticipantMeetingInitiator(
                $this->teamMemberRepository, $this->teamParticipantRepository, $this->participantAttendeeRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMeetingInitiator::class);
    }
    
    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->participantId, $this->attendeeId, $this->task);
    }
    public function test_execute_memberExecuteTask()
    {
        $this->teamMember->expects($this->once())
                ->method('executeTaskAsMemberOfTeamParticipantMeetingInitiator')
                ->with($this->teamParticipant, $this->participantAttendee, $this->task);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamMemberRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
