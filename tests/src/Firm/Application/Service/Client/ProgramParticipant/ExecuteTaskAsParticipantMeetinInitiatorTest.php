<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\Application\Service\Firm\Program\Participant\ParticipantAttendeeRepository;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\ClientParticipant;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Tests\TestBase;

class ExecuteTaskAsParticipantMeetinInitiatorTest extends TestBase
{
    protected $clientParticipantRepository, $clientParticipant, 
            $firmId = 'firm-id', $clientId = 'client-id', $participantId = 'participant-id';
    protected $participantAttendeeRepository, $participantAttendee, $participantAttendeeId = 'participan-attendee-id';
    protected $service;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method('aClientParticipantBelongsToClient')
                ->with($this->firmId, $this->clientId, $this->participantId)
                ->willReturn($this->clientParticipant);
        
        $this->participantAttendee = $this->buildMockOfClass(ParticipantAttendee::class);
        $this->participantAttendeeRepository = $this->buildMockOfInterface(ParticipantAttendeeRepository::class);
        $this->participantAttendeeRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantAttendeeId)
                ->willReturn($this->participantAttendee);
        
        $this->service = new ExecuteTaskAsParticipantMeetinInitiator(
                $this->clientParticipantRepository, $this->participantAttendeeRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMeetingInitiator::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->participantId, $this->participantAttendeeId, $this->task);
    }
    public function test_execute_clientParticipantExecuteTaskAsParticipantMeetinInitiator()
    {
        $this->clientParticipant->expects($this->once())
                ->method('executeTaskAsParticipantMeetinInitiator')
                ->with($this->participantAttendee, $this->task);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
