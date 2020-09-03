<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\Model\Firm\Personnel\PersonnelMailNotification;
use Resources\Domain\ {
    Model\Mail\Recipient,
    ValueObject\PersonName
};
use Tests\TestBase;

class PersonnelTest extends TestBase
{
    protected $personnel;
    protected $personName;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->personnel = new TestablePersonnel();
        $this->personnel->email = 'personnel@email.org';
        
        $this->personName = $this->buildMockOfClass(PersonName::class);
        $this->personnel->name = $this->personName;
    }
    
    public function test_getMailRecipient_returnRecipient()
    {
        $recipient = new Recipient($this->personnel->email, $this->personnel->name);
        $this->assertEquals($recipient, $this->personnel->getMailRecipient());
    }
    public function test_getName_returnFullName()
    {
        $this->personName->expects($this->once())
                ->method('getFullName');
        $this->personnel->getName();
    }
    
    public function test_createMailNotification_returnPersonnelMailNotification()
    {
        $personnelMailNotification = new PersonnelMailNotification($this->personnel);
        $this->assertEquals($personnelMailNotification, $this->personnel->createMailNotification());
    }
}

class TestablePersonnel extends Personnel
{
    public $firm;
    public $id;
    public $name;
    public $email;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
