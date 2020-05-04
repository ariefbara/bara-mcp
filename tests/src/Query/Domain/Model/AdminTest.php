<?php

namespace Query\Domain\Model;

use Resources\Domain\ValueObject\Password;
use Tests\TestBase;

class AdminTest extends TestBase
{
    protected $password;
    protected $admin;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->password = $this->buildMockOfClass(Password::class);
        $this->admin = new TestableAdmin();
        $this->admin->password = $this->password;
    }
    
    public function test_passwordMatched_returnMatchComparisonResultOfPassword()
    {
        $this->password->expects($this->once())
                ->method('match')
                ->with($password = 'password123')
                ->willReturn(true);
        $this->assertTrue($this->admin->passwordMatches($password));
    }
}

class TestableAdmin extends Admin
{

    public $password;

    function __construct()
    {
        parent::__construct();
    }

}
