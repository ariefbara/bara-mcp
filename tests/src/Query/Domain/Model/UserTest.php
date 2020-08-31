<?php

namespace Query\Domain\Model;

use Resources\Domain\ValueObject\Password;
use Tests\TestBase;

class UserTest extends TestBase
{
    protected $password;
    protected $user;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->password = $this->buildMockOfClass(Password::class);
        $this->user = new TestableUser();
        $this->user->password = $this->password;
    }
    
    public function test_passwordMatches_returnResultOfPasswordMatchMethod()
    {
        $this->password->expects($this->once())
                ->method('match')
                ->with($password = 'password123')
                ->willReturn(true);
        $this->assertTrue($this->user->passwordMatches($password));
    }
}

class TestableUser extends User
{
    public $password;
    
    function __construct()
    {
        parent::__construct();
    }
}
