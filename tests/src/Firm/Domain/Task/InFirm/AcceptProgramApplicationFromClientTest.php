<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm\Client\ClientParticipant;
use Firm\Domain\Task\Dependency\Firm\Client\ClientParticipantRepository;
use Resources\Application\Event\AdvanceDispatcher;
use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class AcceptProgramApplicationFromClientTest extends FirmTaskTestBase
{

    protected $clientParticipantRepository;
    protected $clientParticipant;
    protected $clientParticipantId = 'clientParticipantId';
    protected $dispatcher;
    protected $task;
    protected $payload;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setClientRelatedDependency();
        $this->setProgramRelatedDependency();
        
        $this->clientParticipantRepository = $this->buildMockOfClass(ClientParticipantRepository::class);
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        
        $this->dispatcher = $this->buildMockOfClass(AdvanceDispatcher::class);

        $this->task = new AcceptProgramApplicationFromClient($this->clientParticipantRepository,
                $this->clientRepository, $this->programRepository, $this->dispatcher);
        $this->payload = new AcceptProgramApplicationFromClientPayload($this->programId, $this->clientId);
    }

    protected function execute()
    {
        $this->clientParticipantRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->clientParticipantId);
        $this->client->expects($this->any())
                ->method('addAsProgramApplicant')
                ->with($this->clientParticipantId, $this->program)
                ->willReturn($this->clientParticipant);
        $this->task->execute($this->firm, $this->payload);
    }
    public function test_execute_addClientAsProgramApplicant()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method('add')
                ->with($this->clientParticipant);
        $this->execute();
    }
    public function test_execute_setPayloadAcceptedId()
    {
        $this->execute();
        $this->assertSame($this->clientParticipantId, $this->payload->acceptedClientParticipantId);
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
    public function test_execute_dispatcheClientParticipantEvents()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->clientParticipant);
        $this->execute();
    }

}
