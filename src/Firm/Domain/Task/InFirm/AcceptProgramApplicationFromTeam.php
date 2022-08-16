<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Task\Dependency\Firm\ProgramRepository;
use Firm\Domain\Task\Dependency\Firm\Team\TeamParticipantRepository;
use Firm\Domain\Task\Dependency\Firm\TeamRepository;
use Resources\Application\Event\AdvanceDispatcher;

class AcceptProgramApplicationFromTeam implements FirmTask
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

    /**
     * 
     * @var AdvanceDispatcher
     */
    protected $dispatcher;

    public function __construct(
            TeamParticipantRepository $teamParticipantRepository, TeamRepository $teamRepository,
            ProgramRepository $programRepository, AdvanceDispatcher $dispatcher)
    {
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->teamRepository = $teamRepository;
        $this->programRepository = $programRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * 
     * @param Firm $firm
     * @param AcceptProgramApplicationFromTeamPayload $payload
     * @return void
     */
    public function execute(Firm $firm, $payload): void
    {
        $payload->acceptedTeamParticipantId = $this->teamParticipantRepository->nextIdentity();
        $team = $this->teamRepository->ofId($payload->getTeamId());
        $program = $this->programRepository->aProgramOfId($payload->getProgramId());
        
        $team->assertUsableInFirm($firm);
        $program->assertUsableInFirm($firm);
        
        $teamParticipant = $team->addAsProgramApplicant($payload->acceptedTeamParticipantId, $program);
        $this->teamParticipantRepository->add($teamParticipant);
        
        $this->dispatcher->dispatch($teamParticipant);
    }

}
