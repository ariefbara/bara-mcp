<?php

namespace Firm\Application\Listener;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Team;
use Resources\Application\Event\AdvanceDispatcher;
use Tests\TestBase;

class AcceptProgramApplicationFromTeamTest extends TestBase
{

    protected $teamRepository, $team, $teamId = 'teamId';
    protected $programRepository, $program, $programId = 'programId';
    protected $dispatcher;
    protected $listener;
    //
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = $this->buildMockOfInterface(TeamRepository::class);
        $this->team = $this->buildMockOfClass(Team::class);

        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->program = $this->buildMockOfClass(Program::class);
        
        $this->dispatcher = $this->buildMockOfClass(AdvanceDispatcher::class);
        
        $this->listener = new AcceptProgramApplicationFromTeam($this->programRepository, $this->teamRepository, $this->dispatcher);
        
        $this->event = new \Team\Domain\Event\TeamAppliedToProgram($this->teamId, $this->programId);
    }
    
    protected function handle()
    {
        $this->teamRepository->expects($this->any())
                ->method('ofId')
                ->with($this->teamId)
                ->willReturn($this->team);
        
        $this->programRepository->expects($this->any())
                ->method('aProgramOfId')
                ->with($this->programId)
                ->willReturn($this->program);
        
        $this->listener->handle($this->event);
    }
    public function test_handle_programReceiveApplicationFromTeam()
    {
        $this->program->expects($this->once())
                ->method('receiveApplication')
                ->with($this->team);
        $this->handle();
    }
    public function test_handle_updateProgramRepository()
    {
        $this->programRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
    public function test_handle_dispatchProgram()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->program);
        $this->handle();
    }

}
