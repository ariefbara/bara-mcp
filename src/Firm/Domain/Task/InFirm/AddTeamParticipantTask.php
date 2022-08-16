<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FirmTaskExecutableByManager;
use Firm\Domain\Task\Dependency\Firm\ProgramRepository;
use Firm\Domain\Task\Dependency\Firm\TeamRepository;

class AddTeamParticipantTask implements FirmTaskExecutableByManager
{

    /**
     * 
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     * 
     * @var AddTeamParticipantPayload
     */
    protected $payload;

    /**
     * 
     * @var string|null
     */
    public $addedTeamParticipantId;

    public function __construct(
            TeamRepository $teamRepository, ProgramRepository $programRepository, AddTeamParticipantPayload $payload)
    {
        $this->teamRepository = $teamRepository;
        $this->programRepository = $programRepository;
        $this->payload = $payload;
    }

    public function executeInFirm(Firm $firm): void
    {
        $team = $this->teamRepository->ofId($this->payload->getTeamId());
        $team->assertUsableInFirm($firm);
        $program = $this->programRepository->aProgramOfId($this->payload->getProgramId());
        $program->assertUsableInFirm($firm);
        $this->addedTeamParticipantId = $team->addToProgram($program);
    }

}
