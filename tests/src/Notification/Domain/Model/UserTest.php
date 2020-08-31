<?php

namespace Notification\Domain\Model;

use Resources\Domain\ValueObject\PersonName;
use Tests\TestBase;

class UserTest extends TestBase
{
    protected $user;
    protected $personName;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new TestableUser();
        $this->personName = $this->buildMockOfClass(PersonName::class);
        $this->user->personName = $this->personName;
    }
    
    public function test_getName_returnFullName()
    {
        $this->personName->expects($this->once())
                ->method('getFullName');
        $this->user->getName();
    }
}

class TestableUser extends User
{
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
