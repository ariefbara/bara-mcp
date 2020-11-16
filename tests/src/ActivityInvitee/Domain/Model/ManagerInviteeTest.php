<?php

namespace ActivityInvitee\Domain\Model;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ManagerInviteeTest extends TestBase
{
    protected $managerInvitee;
    protected $invitee;
    protected $formRecordData;


    protected function setUp(): void
    {
        parent::setUp();
        $this->managerInvitee = new TestableManagerInvitee();
        $this->invitee = $this->buildMockOfClass(Invitee::class);
        $this->managerInvitee->invitee = $this->invitee;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_submitReport_executeInviteesSubmitReport()
    {
        $this->invitee->expects($this->once())
                ->method("submitReport")
                ->with($this->formRecordData);
        $this->managerInvitee->submitReport($this->formRecordData);
    }
}

class TestableManagerInvitee extends ManagerInvitee
{
    public $manager;
    public $id;
    public $invitee;
    
    function __construct()
    {
        parent::__construct();
    }
}

