<?php

namespace Team\Application\Service\TeamMember;

use Team\Domain\Task\TeamTask;

class ExecuteTeamTask
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    public function __construct(TeamMemberRepository $teamMemberRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
    }
    
    public function execute(string $firmId, string $clientId, string $teamId, TeamTask $task, $payload): void
    {
        $this->teamMemberRepository->aMemberCorrespondWithClient($firmId, $teamId, $clientId)
                ->executeTeamTask($task, $payload);
        $this->teamMemberRepository->update();
    }

}
