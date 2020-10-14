<?php

namespace Query\Domain\Service;

use Query\Domain\Model\Firm\Team;
use Tests\TestBase;

class TeamProgramParticipationFinderTest extends TestBase
{
    protected $teamProgramParticipationRepository;
    protected $finder;
    protected $team, $teamId = "teamId";
    protected $programId = "programId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamProgramParticipationRepository = $this->buildMockOfInterface(TeamProgramParticipationRepository::class);
        $this->finder = new TeamProgramParticipationFinder($this->teamProgramParticipationRepository);
        
        $this->team = $this->buildMockOfClass(Team::class);
        $this->team->expects($this->any())->method("getId")->willReturn($this->teamId);
    }
    
    public function test_execute_returnRepositoryATeamProgramParticipationCorrespondWithProgramResult()
    {
        $this->teamProgramParticipationRepository->expects($this->once())
                ->method("aTeamProgramParticipationCorrespondWithProgram")
                ->with($this->teamId, $this->programId);
        $this->finder->execute($this->team, $this->programId);
    }
}
