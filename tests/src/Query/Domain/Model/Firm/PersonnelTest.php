<?php

namespace Query\Domain\Model\Firm;

use Resources\Domain\ValueObject\Password;
use Tests\TestBase;

class PersonnelTest extends TestBase
{
    protected $password;
    protected $personnel;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->password = $this->buildMockOfClass(Password::class);
        $this->personnel = new TestablePersonnel();
        $this->personnel->password = $this->password;
    }
    
    public function test_passwordMatches_returnPasswordMatchComparisonResult()
    {
        $this->password->expects($this->once())
                ->method('match')
                ->with($password = 'password')
                ->willReturn(true);
        $this->assertTrue($this->personnel->passwordMatches($password));
    }
}

class TestablePersonnel extends Personnel
{
    public $password;
    
    function __construct()
    {
        parent::__construct();
    }
}
