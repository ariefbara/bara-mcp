<?php

namespace Query\Domain\Model\Firm;

use Resources\Domain\ValueObject\Password;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $client;
    protected $password;


    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new TestableClient();
        
        $this->password = $this->buildMockOfClass(Password::class);
        $this->client->password = $this->password;
    }
    
    public function test_passwordMatch_returnPasswordsMatchMetod()
    {
        $this->password->expects($this->once())
                ->method('match')
                ->with($password = 'passowrd123')
                ->willReturn(true);
        $this->assertTrue($this->client->passwordMatch($password));
    }
}

class TestableClient extends Client
{
    public $firm;
    public $id;
    public $personName;
    public $email;
    public $password;
    public $signupTime;
    public $activationCode = null;
    public $activationCodeExpiredTime = null;
    public $resetPasswordCode = null;
    public $resetPasswordCodeExpiredTime = null;
    public $activated = false;
    
    public function __construct()
    {
        parent::__construct();
    }
}
