<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\ProgramsProfileFormRepository;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitParticipantProfileTest extends TestBase
{

    protected $teamParticipantRepository;
    protected $teamMemberRepository, $teamMember;
    protected $programsProfileFormRepository;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $programParticipationId = "clientParticipantId",
            $programsProfileFormId = "programProfileFormId";
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teamParticipantRepository = $this->buildMockOfInterface(TeamParticipantRepository::class);

        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMemberRepository = $this->buildMockOfClass(TeamMemberRepository::class);
        $this->teamMemberRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);

        $this->programsProfileFormRepository = $this->buildMockOfInterface(ProgramsProfileFormRepository::class);

        $this->service = new SubmitParticipantProfile(
                $this->teamParticipantRepository, $this->teamMemberRepository, $this->programsProfileFormRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->programParticipationId,
                $this->programsProfileFormId, $this->formRecordData);
    }
    public function test_execute_teamMemberSubmitParticipantProfile()
    {
        $this->teamParticipantRepository->expects($this->once())->method("ofId")->with($this->programParticipationId);
        $this->programsProfileFormRepository->expects($this->once())->method("ofId")->with($this->programsProfileFormId);
        $this->teamMember->expects($this->once())
                ->method("submitParticipantProfile")
                ->with($this->anything(), $this->anything(), $this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamParticipantRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
