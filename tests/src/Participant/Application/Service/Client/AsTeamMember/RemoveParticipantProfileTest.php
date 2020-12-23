<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\ParticipantProfileRepository;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Tests\TestBase;

class RemoveParticipantProfileTest extends TestBase
{
    protected $participantProfileRepository;
    protected $teamMemberRepository, $teamMember;
    protected $teamParticipantRepository;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $programParticipationId = "participantId",
            $programsProfileFormId = "programProfileFormId";


    protected function setUp(): void
    {
        parent::setUp();
        
        $this->participantProfileRepository = $this->buildMockOfInterface(ParticipantProfileRepository::class);
        
        $this->teamParticipantRepository = $this->buildMockOfInterface(TeamParticipantRepository::class);

        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMemberRepository = $this->buildMockOfClass(TeamMemberRepository::class);
        $this->teamMemberRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);
        
        $this->service = new RemoveParticipantProfile(
                $this->participantProfileRepository, $this->teamMemberRepository, $this->teamParticipantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->programParticipationId, $this->programsProfileFormId);
    }
    public function test_execute_teamMemberRemovedParticipantProfile()
    {
        $this->participantProfileRepository->expects($this->once())
                ->method("aParticipantProfileCorrespondWithProgramsProfileForm")
                ->with($this->programParticipationId, $this->programsProfileFormId);
        $this->teamParticipantRepository->expects($this->once())->method("ofId")->with($this->programParticipationId);
        $this->teamMember->expects($this->once())
                ->method("removeParticipantProfile");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->participantProfileRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
