<?php

namespace Query\Domain\Model\Firm;

use Resources\Domain\ValueObject\Password;
use Tests\TestBase;

class ManagerTest extends TestBase
{
    protected $password;
    protected $manager;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->password = $this->buildMockOfClass(Password::class);
        $this->manager = new TestableManager();
        $this->manager->password = $this->password;
    }
    
    public function test_passwordMatcher_returnPasswordMatchComparisonResult()
    {
        $this->password->expects($this->once())
                ->method('match')
                ->with($password = "password")
                ->willReturn(true);
        $this->assertTrue($this->manager->passwordMatches($password));
    }
}

class TestableManager extends Manager
{
    public $password;
    
    public function __construct()
    {
        parent::__construct();
    }
}
