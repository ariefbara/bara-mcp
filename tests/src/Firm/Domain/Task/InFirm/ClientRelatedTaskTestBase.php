<?php

namespace Tests\src\Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use PHPUnit\Framework\MockObject\MockObject;

class ClientRelatedTaskTestBase extends FirmTaskTestBase
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
    protected $clientId = 'clientId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId)
                ->willReturn($this->client);
    }

}
