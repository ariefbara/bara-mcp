<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class GenerateActivationCodeTest extends TestBase
{

    protected $service;
    protected $clientRepository, $client;
    protected $dispatcher;
    protected $firmIdentifier = 'firm_identifier', $email = 'client@email.org';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->firmIdentifier, $this->email)
                ->willReturn($this->client);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new GenerateActivationCode($this->clientRepository, $this->dispatcher);
    }

    protected function execute()
    {
        $this->service->execute($this->firmIdentifier, $this->email);
    }

    public function test_execute_generateClientActivationCode()
    {
        $this->client->expects($this->once())
                ->method('generateActivationCode');
        $this->execute();
    }

    public function test_execute_updateRepository()
    {
        $this->clientRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

    public function test_execute_dispatchDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->client);
        $this->execute();
    }

}
