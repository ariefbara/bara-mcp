<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ClientGenerateResetPasswordCodeTest extends TestBase
{
    protected $service;
    protected $clientRepository, $client, $clientEmail = 'client@email.org';
    protected $dispatcher;


    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->clientEmail)
                ->willReturn($this->client);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new ClientGenerateResetPasswordCode($this->clientRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->clientEmail);
    }
    public function test_execute_resetClientPassword()
    {
        $this->client->expects($this->once())
                ->method('generateResetPasswordCode');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
    public function test_execute_dispatchClientToDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->client);
        $this->execute();
    }
}
