<?php

namespace Firm\Application\Service\User\ProgramParticipant;

use Firm\Application\Service\Firm\Program\Participant\ParticipantAttendeeRepository;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Firm\Domain\Model\Firm\Program\UserParticipant;
use Tests\TestBase;

class ExecuteTaskAsParticipantMeetinInitiatorTest extends TestBase
{
    protected $userParticipantRepository, $userParticipant, 
            $userId = 'user-id', $participantId = 'participant-id';
    protected $participantAttendeeRepository, $participantAttendee, $participantAttendeeId = 'participan-attendee-id';
    protected $service;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method('aUserParticipantBelongsToUser')
                ->with($this->userId, $this->participantId)
                ->willReturn($this->userParticipant);
        
        $this->participantAttendee = $this->buildMockOfClass(ParticipantAttendee::class);
        $this->participantAttendeeRepository = $this->buildMockOfInterface(ParticipantAttendeeRepository::class);
        $this->participantAttendeeRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantAttendeeId)
                ->willReturn($this->participantAttendee);
        
        $this->service = new ExecuteTaskAsParticipantMeetinInitiator(
                $this->userParticipantRepository, $this->participantAttendeeRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMeetingInitiator::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->participantId, $this->participantAttendeeId, $this->task);
    }
    public function test_execute_userParticipantExecuteTaskAsParticipantMeetinInitiator()
    {
        $this->userParticipant->expects($this->once())
                ->method('executeTaskAsParticipantMeetinInitiator')
                ->with($this->participantAttendee, $this->task);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
