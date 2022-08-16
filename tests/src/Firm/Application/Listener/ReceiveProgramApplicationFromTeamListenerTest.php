<?php

namespace Firm\Application\Listener;

use Firm\Application\Service\FirmRepository;
use Firm\Domain\Model\Firm;
use Firm\Domain\Task\InFirm\AcceptProgramApplicationFromTeam;
use Tests\TestBase;

class ReceiveProgramApplicationFromTeamListenerTest extends TestBase
{
    protected $firmRepository, $firm;
    protected $task;
    protected $listener;
    //
    protected $event, $firmId = 'firmid', $teamId = 'teamId', $programId = 'programId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->firmRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId)
                ->willReturn($this->firm);
        $this->task = $this->buildMockOfClass(AcceptProgramApplicationFromTeam::class);
        
        $this->listener = new ReceiveProgramApplicationFromTeamListener($this->firmRepository, $this->task);
        
        $this->event = new \Team\Domain\Event\TeamHasAppliedToProgram($this->firmId, $this->teamId, $this->programId);
    }
    
    protected function handle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeTask()
    {
        $payload = new \Firm\Domain\Task\InFirm\AcceptProgramApplicationFromTeamPayload($this->teamId, $this->programId);
        $this->task->expects($this->once())
                ->method('execute')
                ->with($this->firm, $payload);
        $this->handle();
    }
    public function test_handle_updateRepository()
    {
        $this->firmRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
}
