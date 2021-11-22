<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class AddClientParticipantTaskTest extends FirmTaskTestBase
{
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setClientRelatedDependency();
        $this->setProgramRelatedDependency();
        
        $payload = new AddClientParticipantPayload($this->clientId, $this->programId);
        $this->task = new AddClientParticipantTask($this->clientRepository, $this->programRepository, $payload);
    }
    
    protected function executeInFirm()
    {
        $this->task->executeInFirm($this->firm);
    }
    public function test_executeInFirm_addClientToProgram()
    {
        $this->client->expects($this->once())
                ->method('addIntoProgram')
                ->with($this->program);
        $this->executeInFirm();
    }
    public function test_executeInFirm_setAddedParticipantIdFromAddingClientIntoProgramResult()
    {
        $this->client->expects($this->once())
                ->method('addIntoProgram')
                ->willReturn($participantId = 'clientParticipantId');
        $this->executeInFirm();
        $this->assertSame($participantId, $this->task->addedClientParticipantId);
    }
    public function test_executeInFirm_assertClientUsableInFirm()
    {
        $this->client->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->executeInFirm();
    }
    public function test_executeInFirm_assertProgramUsableInFirm()
    {
        $this->program->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->executeInFirm();
    }
}
