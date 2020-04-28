<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;
use Tests\TestBase;

class ClientChangePasswordTest extends TestBase
{
    protected $service;
    protected $clientRepository, $client, $clientId = 'clientId';
    protected $previousPassword = "oldPwd123", $newPassword = 'newPwd123';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId)
                ->willReturn($this->client);
        
        $this->service = new ClientChangePassword($this->clientRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->clientId, $this->previousPassword, $this->newPassword);
    }
    
    public function test_execute_changeClientPassword()
    {
        $this->client->expects($this->once())
                ->method('changePassword')
                ->with($this->previousPassword, $this->newPassword);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
