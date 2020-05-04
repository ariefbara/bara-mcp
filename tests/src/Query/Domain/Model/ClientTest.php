<?php

namespace Query\Domain\Model;

use Resources\Domain\ValueObject\Password;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $password;
    protected $client;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->password = $this->buildMockOfClass(Password::class);
        $this->client = new TestableClient();
        $this->client->password = $this->password;
    }
    
    public function test_passwordMatches_returnResultOfPasswordMatchMethod()
    {
        $this->password->expects($this->once())
                ->method('match')
                ->with($password = 'password123')
                ->willReturn(true);
        $this->assertTrue($this->client->passwordMatches($password));
    }
}

class TestableClient extends Client
{
    public $password;
    
    function __construct()
    {
        parent::__construct();
    }
}
