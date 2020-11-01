<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use Participant\Domain\DependencyModel\Firm\Client;
use Tests\TestBase;

class ClientFileInfoTest extends TestBase
{
    protected $clientFileInfo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientFileInfo = new TestableClientFileInfo();
        $this->clientFileInfo->client = $this->buildMockOfClass(Client::class);
    }
    
    public function test_belongsToClient_sameClient_returnTrue()
    {
        $this->assertTrue($this->clientFileInfo->belongsToClient($this->clientFileInfo->client));
    }
    public function test_belongsToClient_differentClient_returnFalse()
    {
        $client = $this->buildMockOfClass(Client::class);
        $this->assertFalse($this->clientFileInfo->belongsToClient($client));
    }
}

class TestableClientFileInfo extends ClientFileInfo
{
    public $client;
    public $id;
    public $fileInfo;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
