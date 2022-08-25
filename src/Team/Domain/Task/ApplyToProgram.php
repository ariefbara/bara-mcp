<?php

namespace Team\Domain\Task;

use Resources\Application\Event\AdvanceDispatcher;
use Team\Domain\Model\Team;

class ApplyToProgram implements TeamTask
{

    /**
     * 
     * @var AdvanceDispatcher
     */
    protected $dispatcher;

    public function __construct(AdvanceDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * 
     * @param Team $team
     * @param string $payload programId
     * @return void
     */
    public function execute(Team $team, $payload): void
    {
        $team->applyToProgram($payload);
        $this->dispatcher->dispatch($team);
    }

}
