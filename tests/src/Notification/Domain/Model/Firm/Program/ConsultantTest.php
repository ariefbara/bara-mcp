<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\ {
    Personnel,
    Program\Consultant\ConsultantMailNotification
};
use Tests\TestBase;

class ConsultantTest extends TestBase
{
    protected $consultant;
    protected $personnel;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = new TestableConsultant();
        
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->consultant->personnel = $this->personnel;
    }
    
    public function test_getPersonnelMailRecipient_returnPersonnelsGetMailRecipientResult()
    {
        $this->personnel->expects($this->once())
                ->method('getMailRecipient');
        $this->consultant->getPersonnelMailRecipient();
    }
    
    public function test_getPersonnelName_returnPersonnelName()
    {
        $this->personnel->expects($this->once())
                ->method('getName');
        $this->consultant->getPersonnelName();
        
    }
    
    public function test_createMailNotification_returnConsultantMailNotification()
    {
        $personnelMailNotification = $this->buildMockOfClass(Personnel\PersonnelMailNotification::class);
        $this->personnel->expects($this->once())
                ->method('createMailNotification')
                ->willReturn($personnelMailNotification);
        
        $consultantMailNotification = new ConsultantMailNotification($this->consultant, $personnelMailNotification);
        
        $this->assertEquals($consultantMailNotification, $this->consultant->createMailNotification());
    }
}

class TestableConsultant extends Consultant
{
    public $program;
    public $id;
    public $personnel;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
