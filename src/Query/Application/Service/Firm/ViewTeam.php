<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Team;

class ViewTeam
{

    /**
     *
     * @var TeamRepository
     */
    protected $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param int $page
     * @param int $pageSize
     * @return Team[]
     */
    public function showAll(string $firmId, int $page, int $pageSize)
    {
        return $this->teamRepository->all($firmId, $page, $pageSize);
    }

    public function showById(string $firmId, string $teamId): Team
    {
        return $this->teamRepository->ofId($firmId, $teamId);
    }

}
