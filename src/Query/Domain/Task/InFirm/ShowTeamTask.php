<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Task\Dependency\Firm\TeamRepository;

class ShowTeamTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Team|null
     */
    public $result;

    public function __construct(TeamRepository $teamRepository, string $id)
    {
        $this->teamRepository = $teamRepository;
        $this->id = $id;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $this->result = $this->teamRepository->aTeamInFirm($firm->getId(), $this->id);
    }

}
