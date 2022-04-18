<?php

namespace Tests\src\Client\Domain\Task;

use Client\Domain\Model\Client;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class ClientTaskTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $client;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
    }
}
