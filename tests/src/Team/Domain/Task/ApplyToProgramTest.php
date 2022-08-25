<?php

namespace Team\Domain\Task;

use Resources\Application\Event\AdvanceDispatcher;
use Team\Domain\Model\Team;
use Tests\TestBase;

class ApplyToProgramTest extends TestBase
{
    protected $dispatcher;
    protected $task;
    
    protected $team;
    protected $programId = 'programId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatcher = $this->buildMockOfClass(AdvanceDispatcher::class);
        $this->task = new ApplyToProgram($this->dispatcher);
        
        $this->team = $this->buildMockOfClass(Team::class);
    }
    
    protected function execute()
    {
        $this->task->execute($this->team, $this->programId);
    }
    public function test_execute_registerTeamToProgram()
    {
        $this->team->expects($this->once())
                ->method('applyToProgram')
                ->with($this->programId);
        $this->execute();
    }
    public function test_execute_dispatchTeam()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->team);
        $this->execute();
    }
}
