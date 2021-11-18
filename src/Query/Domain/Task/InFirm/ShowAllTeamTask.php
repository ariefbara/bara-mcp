<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Task\Dependency\Firm\TeamFilter;
use Query\Domain\Task\Dependency\Firm\TeamRepository;

class ShowAllTeamTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * 
     * @var TeamFilter
     */
    protected $payload;

    /**
     * 
     * @var Team[]|null
     */
    public $results;

    public function __construct(TeamRepository $teamRepository, TeamFilter $payload)
    {
        $this->teamRepository = $teamRepository;
        $this->payload = $payload;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $this->results = $this->teamRepository->allTeamInFirm($firm->getId(), $this->payload);
    }

}
