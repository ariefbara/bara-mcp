<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Task\Dependency\Firm\ProgramRepository;
use Firm\Domain\Task\Dependency\Firm\Team\TeamParticipantRepository;
use Firm\Domain\Task\Dependency\Firm\TeamRepository;

class AddTeamAsActiveProgramParticipant implements FirmTask
{

    /**
     * 
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

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

    public function __construct(
            TeamParticipantRepository $teamParticipantRepository, TeamRepository $teamRepository,
            ProgramRepository $programRepository)
    {
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->teamRepository = $teamRepository;
        $this->programRepository = $programRepository;
    }

    /**
     * 
     * @param Firm $firm
     * @param AddTeamAsActiveProgramParticipantPayload $payload
     * @return void
     */
    public function execute(Firm $firm, $payload): void
    {
        $payload->addedTeamParticipantId = $this->teamParticipantRepository->nextIdentity();
        $team = $this->teamRepository->ofId($payload->getTeamId());
        $program = $this->programRepository->aProgramOfId($payload->getProgramId());
        
        $team->assertUsableInFirm($firm);
        $program->assertUsableInFirm($firm);
        
        $teamParticipant = $team->addAsActiveProgramParticipant($payload->addedTeamParticipantId, $program);
        $this->teamParticipantRepository->add($teamParticipant);
    }

}
