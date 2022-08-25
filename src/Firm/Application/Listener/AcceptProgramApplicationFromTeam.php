<?php

namespace Firm\Application\Listener;

use Resources\Application\Event\AdvanceDispatcher;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Team\Domain\Event\TeamAppliedToProgram;

class AcceptProgramApplicationFromTeam implements Listener
{

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     * 
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * 
     * @var AdvanceDispatcher
     */
    protected $dispatcher;

    public function __construct(
            ProgramRepository $programRepository, TeamRepository $teamRepository, AdvanceDispatcher $dispatcher)
    {
        $this->programRepository = $programRepository;
        $this->teamRepository = $teamRepository;
        $this->dispatcher = $dispatcher;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(TeamAppliedToProgram $event): void
    {
        $program = $this->programRepository->aProgramOfId($event->getProgramId());
        $team = $this->teamRepository->ofId($event->getTeamId());
        $program->receiveApplication($team);
        $this->programRepository->update();
        
        $this->dispatcher->dispatch($program);
    }

}
