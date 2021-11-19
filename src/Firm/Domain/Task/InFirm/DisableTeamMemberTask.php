<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FirmTaskExecutableByManager;
use Firm\Domain\Task\Dependency\Firm\TeamRepository;

class DisableTeamMemberTask implements FirmTaskExecutableByManager
{

    /**
     * 
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * 
     * @var DisableTeamMemberPayload
     */
    protected $payload;

    public function __construct(TeamRepository $teamRepository, DisableTeamMemberPayload $payload)
    {
        $this->teamRepository = $teamRepository;
        $this->payload = $payload;
    }

    public function executeInFirm(Firm $firm): void
    {
        $team = $this->teamRepository->ofId($this->payload->getTeamId());
        $team->assertManageableInFirm($firm);
        $team->disableMember($this->payload->getMemberId());
    }

}
