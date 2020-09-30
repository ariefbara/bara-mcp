<?php

namespace Query\Domain\Service\Firm\Team;

use Query\Domain\Model\Firm\ {
    Team,
    Team\TeamFileInfo
};

class TeamFileInfoFinder
{

    /**
     *
     * @var TeamFileInfoRepository
     */
    protected $teamFileInfoRepository;

    public function __construct(TeamFileInfoRepository $teamFileInfoRepository)
    {
        $this->teamFileInfoRepository = $teamFileInfoRepository;
    }

    public function findFileInfoBelongsToTeam(Team $team, string $teamFileInfoId): TeamFileInfo
    {
        return $this->teamFileInfoRepository->aFileInfoBelongsToTeam($team->getId(), $teamFileInfoId);
    }

}
