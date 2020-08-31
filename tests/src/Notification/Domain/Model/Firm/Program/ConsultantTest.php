<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\Personnel;
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
