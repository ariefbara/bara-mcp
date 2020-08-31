<?php

namespace Client\Application\Service;

use Client\Dommain\Model\Client;
use Tests\TestBase;

class ActivateAccountTest extends TestBase
{
    protected $service;
    protected $clientRepository, $client;
    protected $firmIdentifier = 'firm_identifier', $email = 'client@email.org', $activationCode = 'activationCode';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->firmIdentifier, $this->email)
                ->willReturn($this->client);

        $this->service = new ActivateAccount($this->clientRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmIdentifier, $this->email, $this->activationCode);
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
