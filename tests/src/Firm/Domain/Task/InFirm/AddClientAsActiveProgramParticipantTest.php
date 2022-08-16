<?php

namespace Firm\Domain\Task\InFirm;

use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class AddClientAsActiveProgramParticipantTest extends FirmTaskTestBase
{
    protected $task;
    protected $payload;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setClientParticipantRelatedDependency();
        $this->setClientRelatedDependency();
        $this->setProgramRelatedDependency();
        
        $this->task = new AddClientAsActiveProgramParticipant(
                $this->clientParticipantRepository, $this->clientRepository, $this->programRepository);
        
        $this->payload = new AddClientAsActiveProgramParticipantPayload($this->clientId, $this->programId);
    }
    
    protected function execute()
    {
        $this->clientParticipantRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->clientParticipantId);
        $this->task->execute($this->firm, $this->payload);
    }
    public function test_execute_addClientParticipantCreatedInClientToRepository()
    {
        $this->client->expects($this->once())
                ->method('addAsActiveProgramParticipant')
                ->with($this->clientParticipantId, $this->program)
                ->willReturn($this->clientParticipant);
        $this->clientParticipantRepository->expects($this->once())
                ->method('add')
                ->with($this->clientParticipant);
        $this->execute();
    }
    public function test_execute_setAddedClientParticipantId()
    {
        $this->execute();
        $this->assertSame($this->clientParticipantId, $this->payload->addedClientParticipantId);
    }
    public function test_execute_assertClientUsableInFirm()
    {
        $this->client->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->execute();
    }
    public function test_execute_assertProgramUsableInFirm()
    {
        $this->program->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->execute();
    }
}
