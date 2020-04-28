<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;
use Tests\TestBase;

class ClientActivateTest extends TestBase
{
    protected $service;
    protected $clientRepository, $client, $clientEmail = 'client@email.org';
    protected $activationCode = 'string represent activation code';

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->clientEmail)
                ->willReturn($this->client);
        
        $this->service = new ClientActivate($this->clientRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->clientEmail, $this->activationCode);
    }
    
    public function test_execute_activateClient()
    {
        $this->client->expects($this->once())
                ->method('activate')
                ->with($this->activationCode);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
