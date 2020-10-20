<?php

namespace Query\Application\Service\Firm\Team;

use Query\Domain\Model\Firm\Team\TeamFileInfo;

class ViewTeamFileInfo
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
    
    public function showById(string $teamId, string $teamFileInfoId): TeamFileInfo
    {
        return $this->teamFileInfoRepository->aFileInfoBelongsToTeam($teamId, $teamFileInfoId);
    }

}
