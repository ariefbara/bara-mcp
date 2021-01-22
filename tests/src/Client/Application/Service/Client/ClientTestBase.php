<?php

namespace Tests\src\Client\Application\Service\Client;

use Client\Application\Service\ClientRepository;
use Client\Domain\Model\Client;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class ClientTestBase extends TestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $client;

    /**
     * 
     * @var MockObject
     */
    protected $clientRepository;
    protected $firmId = "firmId", $clientId = "clientId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfClass(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->clientId)
                ->willReturn($this->client);
    }

}
