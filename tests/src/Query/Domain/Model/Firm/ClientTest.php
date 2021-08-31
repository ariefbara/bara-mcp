<?php

namespace Query\Domain\Model\Firm;

use Resources\Domain\ValueObject\Password;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $client;
    protected $password;
    
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new TestableClient();
        
        $this->password = $this->buildMockOfClass(Password::class);
        $this->client->password = $this->password;
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByClient::class);
    }
    
    public function test_passwordMatch_returnPasswordsMatchMetod()
    {
        $this->password->expects($this->once())
                ->method('match')
                ->with($password = 'passowrd123')
                ->willReturn(true);
        $this->assertTrue($this->client->passwordMatch($password));
    }
    
    protected function executeTask()
    {
        $this->client->executeTask($this->task);
    }
    public function test_executeTask_executeTask()
    {
        $this->task->expects($this->once())
                ->method('execute')
                ->with($this->client->id);
        $this->executeTask();
    }
    public function test_executeTask_inactiveClient_403()
    {
        $this->client->activated = false;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeTask();
        }, 'Forbidden', 'forbidden: only active client can make this request');
    }
}

class TestableClient extends Client
{
    public $firm;
    public $id = 'client-id';
    public $personName;
    public $email;
    public $password;
    public $signupTime;
    public $activationCode = null;
    public $activationCodeExpiredTime = null;
    public $resetPasswordCode = null;
    public $resetPasswordCodeExpiredTime = null;
    public $activated = true;
    
    public function __construct()
    {
        parent::__construct();
    }
}
