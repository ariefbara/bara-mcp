<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;
use Tests\TestBase;

class ChangePasswordTest extends TestBase
{
    protected $service;
    protected $clientRepository, $client;
    protected $dispatcher;
    protected $firmId = 'firm-id', $clientId = 'client-id',
            $previousPassword = 'previousPassword', $newPassword = 'newPwd123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->clientId)
                ->willReturn($this->client);
        
        $this->service = new ChangePassword($this->clientRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->previousPassword, $this->newPassword);
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
