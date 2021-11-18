<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm\ClientRegistrationData;
use Tests\src\Firm\Domain\Task\InFirm\ClientRelatedTaskTestBase;

class AddClientTaskTest extends ClientRelatedTaskTestBase
{
    protected $payload;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->payload = new ClientRegistrationData('firstname', 'lastname', 'client@email.org', 'password123');
        $this->task = new AddClientTask($this->clientRepository, $this->payload);
    }
    
    protected function executeInFirm()
    {
        $this->clientRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->clientId);
        $this->task->executeInFirm($this->firm);
    }
    public function test_executeInFirm_addClientCreatedInFirmToRepository()
    {
        $this->firm->expects($this->once())
                ->method('createClient')
                ->with($this->clientId, $this->payload)
                ->willReturn($this->client);
        $this->clientRepository->expects($this->once())
                ->method('add')
                ->with($this->client);
        $this->executeInFirm();
    }
    public function test_executeInFirm_setNewClientIdAsAddedClientId()
    {
        $this->executeInFirm();
        $this->assertEquals($this->clientId, $this->task->addedClientId);
    }
}
