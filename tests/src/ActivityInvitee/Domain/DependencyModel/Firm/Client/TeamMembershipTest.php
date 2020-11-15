<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Client;

use ActivityInvitee\Domain\Model\ParticipantInvitee;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class TeamMembershipTest extends TestBase
{
    protected $teamMembership;
    protected $invitation;
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMembership = new TestableTeamMembership();
        
        $this->invitation = $this->buildMockOfClass(ParticipantInvitee::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function executeSubmitInviteeReportId()
    {
        $this->invitation->expects($this->any())
                ->method("belongsToTeam")
                ->willReturn(true);
        $this->teamMembership->submitInviteeReportIn($this->invitation, $this->formRecordData);
    }
    public function test_submitInviteeReportIn_submitReportInInvitation()
    {
        $this->invitation->expects($this->once())
                ->method("submitReport")
                ->with($this->formRecordData);
        $this->executeSubmitInviteeReportId();
    }
    public function test_submitInviteeReportIn_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $operation = function (){
            $this->executeSubmitInviteeReportId();
        };
        $errorDetail = "forbidden: only active team member can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_submitReportIn_invitationNotBelongsToTeam_forbidden()
    {
        $this->invitation->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->teamMembership->teamId)
                ->willReturn(false);
        $operation = function (){
            $this->executeSubmitInviteeReportId();
        };
        $errorDetail = "forbidden: only allowed to submit report in invitation belongs to team";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}

class TestableTeamMembership extends TeamMembership
{
    public $client;
    public $id;
    public $teamId = "teamId";
    public $active = true;
    
    function __construct()
    {
        parent::__construct();
    }
}
