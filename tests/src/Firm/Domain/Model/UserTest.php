<?php

namespace Firm\Domain\Model;

use Resources\Domain\ {
    Model\Mail\Recipient,
    ValueObject\PersonName
};
use Tests\TestBase;

class UserTest extends TestBase
{
    protected $user;
    protected $personName;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new TestableUser();
        $this->user->email = 'user@email.org';
        $this->personName = $this->buildMockOfClass(PersonName::class);
        $this->user->personName = $this->personName;
    }
    
    public function test_getMailRecipient_returnRecipient()
    {
        $recipient = new Recipient($this->user->email, $this->personName);
        $this->assertEquals($recipient, $this->user->getMailRecipient());
    }
    
    public function test_getName_returnPersonNameFullName()
    {
        $this->personName->expects($this->once())
                ->method('getFullName')
                ->willReturn('hadi pranoto');
        $this->assertEquals('hadi pranoto', $this->user->getName());
    }
}

class TestableUser extends User
{
    public $id;
    public $personName;
    public $email;
    
    function __construct()
    {
        parent::__construct();
    }
}
