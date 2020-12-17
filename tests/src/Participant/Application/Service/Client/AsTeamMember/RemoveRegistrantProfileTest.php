<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\RegistrantProfileRepository;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Tests\TestBase;

class RemoveRegistrantProfileTest extends TestBase
{
    protected $registrantProfileRepository;
    protected $teamMemberRepository, $teamMember;
    protected $teamRegistrantRepository;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $programRegistrationId = "clientRegistrantId",
            $programsProfileFormId = "programProfileFormId";


    protected function setUp(): void
    {
        parent::setUp();
        
        $this->registrantProfileRepository = $this->buildMockOfInterface(RegistrantProfileRepository::class);
        
        $this->teamRegistrantRepository = $this->buildMockOfInterface(TeamRegistrantRepository::class);

        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMemberRepository = $this->buildMockOfClass(TeamMemberRepository::class);
        $this->teamMemberRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);
        
        $this->service = new RemoveRegistrantProfile(
                $this->registrantProfileRepository, $this->teamMemberRepository, $this->teamRegistrantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->programRegistrationId, $this->programsProfileFormId);
    }
    public function test_execute_teamMemberRemovedRegistrantProfile()
    {
        $this->registrantProfileRepository->expects($this->once())
                ->method("aRegistrantProfileCorrespondWithProgramsProfileForm")
                ->with($this->programRegistrationId, $this->programsProfileFormId);
        $this->teamRegistrantRepository->expects($this->once())->method("ofId")->with($this->programRegistrationId);
        $this->teamMember->expects($this->once())
                ->method("removeRegistrantProfile");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->registrantProfileRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
