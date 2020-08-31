<?php

namespace Notification\Domain\Model\Firm;

use Resources\Domain\ValueObject\PersonName;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $client;
    protected $personName;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new TestableClient();
        $this->personName = $this->buildMockOfClass(PersonName::class);
        $this->client->personName = $this->personName;
    }
    
    public function test_getName_returnFullName()
    {
        $this->personName->expects($this->once())
                ->method('getFullName');
        $this->client->getName();
    }
}

class TestableClient extends Client
{
    public $firm;
    public $id;
    public $personName;
    public $email;
    public $activationCode = null;
    public $resetPasswordCode = null;
    public $activated = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
