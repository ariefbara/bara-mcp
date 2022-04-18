<?php

namespace Client\Domain\Task;

use Client\Domain\DependencyModel\Firm\Program\Registrant;
use Client\Domain\Model\Client\ClientRegistrant;
use Client\Domain\Task\Repository\Firm\Client\ClientRegistrantRepository;
use Client\Domain\Task\Repository\Firm\Program\RegistrantRepository;
use Tests\src\Client\Domain\Task\ClientTaskTestBase;

class AddClientRegistrantTest extends ClientTaskTestBase
{
    protected $clientRegistrantRepository, $clientRegistrant;
    protected $registrantRepository, $registrant, $registrantId = 'registrant-id';
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrantRepository = $this->buildMockOfClass(ClientRegistrantRepository::class);
        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        
        $this->registrantRepository = $this->buildMockOfInterface(RegistrantRepository::class);
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->registrantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->registrantId)
                ->willReturn($this->registrant);
        
        $this->task = new AddClientRegistrant($this->clientRegistrantRepository, $this->registrantRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->client, $this->registrantId);
    }
    public function test_execute_addClientRegistrantCreatedInClientToRepository()
    {
        $this->client->expects($this->once())
                ->method('createClientRegistrant')
                ->with($this->registrantId, $this->registrant)
                ->willReturn($this->clientRegistrant);
        $this->clientRegistrantRepository->expects($this->once())
                ->method('add')
                ->with($this->clientRegistrant);
        $this->execute();
    }
    public function test_execute_setAddedClientRegistrantId()
    {
        $this->execute();
        $this->assertSame($this->registrantId, $this->task->addedClientRegistrantId);
    }
}
