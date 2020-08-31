<?php

namespace Client\Application\Service;

use Client\Dommain\Model\Client;
use Tests\TestBase;

class UpdateProfileTest extends TestBase
{
    protected $service;
    protected $clientRepository, $client;
    protected $dispatcher;
    protected $firmId = 'firm-id', $clientId = 'client-id',
            $firstName = 'firstname', $lastName = 'lastname';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->clientId)
                ->willReturn($this->client);
        
        $this->service = new UpdateProfile($this->clientRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->firstName, $this->lastName);
    }
    public function test_execute_udpateClientProfile()
    {
        $this->client->expects($this->once())
                ->method('updateProfile')
                ->with($this->firstName, $this->lastName);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
