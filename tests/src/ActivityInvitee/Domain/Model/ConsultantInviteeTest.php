<?php

namespace ActivityInvitee\Domain\Model;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ConsultantInviteeTest extends TestBase
{
    protected $consultantInvitee;
    protected $invitee;
    protected $formRecordData;


    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantInvitee = new TestableConsultantInvitee();
        $this->invitee = $this->buildMockOfClass(Invitee::class);
        $this->consultantInvitee->invitee = $this->invitee;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_submitReport_executeInviteesSubmitReport()
    {
        $this->invitee->expects($this->once())
                ->method("submitReport")
                ->with($this->formRecordData);
        $this->consultantInvitee->submitReport($this->formRecordData);
    }
}

class TestableConsultantInvitee extends ConsultantInvitee
{
    public $consultant;
    public $id;
    public $invitee;
    
    function __construct()
    {
        parent::__construct();
    }
}
