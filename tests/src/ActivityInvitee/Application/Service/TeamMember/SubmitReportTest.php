<?php

namespace ActivityInvitee\Application\Service\TeamMember;

use ActivityInvitee\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    Model\ParticipantInvitee
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitReportTest extends TestBase
{
    protected $teamMemberRepository, $teamMember;
    protected $activityInvitationRepository, $invitation;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $invitationId = "invitationId";
    protected $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMemberRepository = $this->buildMockOfInterface(TeamMemberRepository::class);
        $this->teamMemberRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);
        
        $this->invitation = $this->buildMockOfClass(ParticipantInvitee::class);
        $this->activityInvitationRepository = $this->buildMockOfInterface(ActivityInvitationRepository::class);
        $this->activityInvitationRepository->expects($this->any())
                ->method("ofId")
                ->with($this->invitationId)
                ->willReturn($this->invitation);
        
        $this->service = new SubmitReport($this->activityInvitationRepository, $this->teamMemberRepository);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->invitationId, $this->formRecordData);
    }
    public function test_execute_executeTeamMemberSubmitInviteeReportIn()
    {
        $this->teamMember->expects($this->once())
                ->method("submitInviteeReportIn")
                ->with($this->invitation, $this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->activityInvitationRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
