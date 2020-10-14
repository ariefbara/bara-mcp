<?php

namespace Query\Application\Service\Firm\Client\AsTeamMember\AsProgramParticipant;

use Query\ {
    Application\Service\Firm\Client\AsTeamMember\TeamMemberRepository,
    Domain\Model\Firm\Team\Member,
    Domain\Service\LearningMaterialFinder,
    Domain\Service\TeamProgramParticipationFinder
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ViewLearningMaterialDetailTest extends TestBase
{

    protected $teamMemberRepository, $teamMember;
    protected $teamProgramParticipationFinder;
    protected $learningMaterialFinder;
    protected $dispatcher;
    protected $service;
    protected $clientId = "clientId", $teamId = "teamId", $programId = "programId", $learningMaterialId = "learningMaterialId";

    protected function setUp(): void
    {
        parent::setUp();

        $this->teamMember = $this->buildMockOfClass(Member::class);
        $this->teamMemberRepository = $this->buildMockOfClass(TeamMemberRepository::class);
        $this->teamMemberRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->clientId, $this->teamId)
                ->willReturn($this->teamMember);

        $this->teamProgramParticipationFinder = $this->buildMockOfClass(TeamProgramParticipationFinder::class);
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ViewLearningMaterialDetail(
                $this->teamMemberRepository, $this->teamProgramParticipationFinder, $this->learningMaterialFinder,
                $this->dispatcher);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->clientId, $this->teamId, $this->programId, $this->learningMaterialId);
    }
    public function test_execute_returnTeamMemberViewLearningMaterialResult()
    {
        $this->teamMember->expects($this->once())
                ->method("viewLearningMaterial")
                ->with($this->teamProgramParticipationFinder, $this->programId, $this->learningMaterialFinder, $this->learningMaterialId);
        $this->execute();
    }
    public function test_execute_dispatchTeamMember()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->teamMember);
        $this->execute();
    }

}
