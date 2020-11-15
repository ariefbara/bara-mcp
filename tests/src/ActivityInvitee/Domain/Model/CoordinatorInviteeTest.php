<?php

namespace ActivityInvitee\Domain\Model;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class CoordinatorInviteeTest extends TestBase
{
    protected $coordinatorInvitee;
    protected $invitee;
    protected $formRecordData;


    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorInvitee = new TestableCoordinatorInvitee();
        $this->invitee = $this->buildMockOfClass(Invitee::class);
        $this->coordinatorInvitee->invitee = $this->invitee;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_submitReport_executeInviteesSubmitReport()
    {
        $this->invitee->expects($this->once())
                ->method("submitReport")
                ->with($this->formRecordData);
        $this->coordinatorInvitee->submitReport($this->formRecordData);
    }
}

class TestableCoordinatorInvitee extends CoordinatorInvitee
{
    public $coordinator;
    public $id;
    public $invitee;
    
    function __construct()
    {
        parent::__construct();
    }
}
