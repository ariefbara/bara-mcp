<?php

namespace Firm\Domain\Task\InFirm;

use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class AddTeamParticipantTaskTest extends FirmTaskTestBase
{
    protected $payload;
    protected $task;
    protected function setUp(): void
    {
        parent::setUp();
        $this->setTeamRelatedDependency();
        $this->setProgramRelatedDependency();
        
        $this->payload = new AddTeamParticipantPayload($this->teamId, $this->programId);
        $this->task = new AddTeamParticipantTask($this->teamRepository, $this->programRepository, $this->payload);
    }
    
    protected function executeInFirm()
    {
        $this->task->executeInFirm($this->firm);
    }
    public function test_executeInFirm_addTeamToProgram()
    {
        $this->team->expects($this->once())
                ->method('addToProgram')
                ->with($this->program);
        $this->executeInFirm();
    }
    public function test_executeInFirm_setAddedTeamParticipantId()
    {
        $this->team->expects($this->once())
                ->method('addToProgram')
                ->willReturn($teamParticipantId = 'teamParticipantId');
        $this->executeInFirm();
        $this->assertSame($teamParticipantId, $this->task->addedTeamParticipantId);
    }
    public function test_executeInFirm_assertTeamUsableInFirm()
    {
        $this->team->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->executeInFirm();
    }
    public function test_executeInFirm_assertProgramUsableInFirm()
    {
        $this->program->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->executeInFirm();
    }
    
}
