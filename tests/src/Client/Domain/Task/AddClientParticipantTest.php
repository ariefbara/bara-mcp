<?php

namespace Client\Domain\Task;

use Client\Domain\DependencyModel\Firm\Program\Participant;
use Client\Domain\Model\Client\ClientParticipant;
use Client\Domain\Task\Repository\Firm\Client\ClientParticipantRepository;
use Client\Domain\Task\Repository\Firm\Program\ParticipantRepository;
use Tests\src\Client\Domain\Task\ClientTaskTestBase;

class AddClientParticipantTest extends ClientTaskTestBase
{
    protected $clientParticipantRepository, $clientParticipant;
    protected $participantRepository, $participant, $participantId = 'participant-id';
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        //
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantId)
                ->willReturn($this->participant);
        $this->task = new AddClientParticipant($this->clientParticipantRepository, $this->participantRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->client, $this->participantId);
    }
    public function test_execute_addClientParticipantCreatedInClientToRepository()
    {
        $this->client->expects($this->once())
                ->method('createClientParticipant')
                ->with($this->participantId, $this->participant)
                ->willReturn($this->clientParticipant);
        $this->clientParticipantRepository->expects($this->once())
                ->method('add')
                ->with($this->clientParticipant);
        $this->execute();
    }
    public function test_execute_setAddedClientParticipantId()
    {
        $this->execute();
        $this->assertSame($this->participantId, $this->task->addedClientParticipantId);
    }
    
}
