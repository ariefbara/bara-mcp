<?php

namespace Tests\src\Query\Application\Service\Client;

use PHPUnit\Framework\MockObject\MockObject;
use Query\Application\Service\Client\ClientRepository;
use Query\Domain\Model\Firm\Client;
use Tests\TestBase;

class ClientTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $clientRepository;
    /**
     * 
     * @var MockObject
     */
    protected $client;
    protected $firmId = 'firm-id', $clientId = 'client-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('aClientInFirm')
                ->with($this->firmId, $this->clientId)
                ->willReturn($this->client);
    }
}
