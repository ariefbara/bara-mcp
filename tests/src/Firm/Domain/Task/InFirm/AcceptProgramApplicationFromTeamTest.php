<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm\Team\TeamParticipant;
use Firm\Domain\Task\Dependency\Firm\Team\TeamParticipantRepository;
use Resources\Application\Event\AdvanceDispatcher;
use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class AcceptProgramApplicationFromTeamTest extends FirmTaskTestBase
{
    protected $teamParticipantRepository;
    protected $teamParticipant;
    protected $teamParticipantId = 'teamParticipantId';
    protected $dispatcher;
    protected $task;
    protected $payload;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setTeamRelatedDependency();
        $this->setProgramRelatedDependency();
        
        $this->teamParticipantRepository = $this->buildMockOfInterface(TeamParticipantRepository::class);
        $this->teamParticipant = $this->buildMockOfClass(TeamParticipant::class);
        
        $this->dispatcher = $this->buildMockOfClass(AdvanceDispatcher::class);
        
        $this->task = new AcceptProgramApplicationFromTeam(
                $this->teamParticipantRepository, $this->teamRepository, $this->programRepository, $this->dispatcher);
        
        $this->payload = new AcceptProgramApplicationFromTeamPayload($this->teamId, $this->programId);
    }
    
    protected function execute()
    {
        $this->teamParticipantRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->teamParticipantId);
        $this->team->expects($this->any())
                ->method('addAsProgramApplicant')
                ->with($this->teamParticipantId, $this->program)
                ->willReturn($this->teamParticipant);
        $this->task->execute($this->firm, $this->payload);
    }
    public function test_execute_addTeamParticipantAddedInTeamToRepository()
    {
        $this->teamParticipantRepository->expects($this->once())
                ->method('add')
                ->with($this->teamParticipant);
        $this->execute();
    }
    public function test_execute_setAcceptedParticipantId()
    {
        $this->execute();
        $this->assertSame($this->teamParticipantId, $this->payload->acceptedTeamParticipantId);
    }
    public function test_execute_dispatchTeamParticipant()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->teamParticipant);
        $this->execute();
    }
    public function test_execute_assertTeamUsableInFirm()
    {
        $this->team->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->execute();
    }
    public function test_execute_assertProgramUsableInFirm()
    {
        $this->program->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->execute();
    }
}
