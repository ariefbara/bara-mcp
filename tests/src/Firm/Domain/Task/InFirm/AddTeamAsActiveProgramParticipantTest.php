<?php

namespace Firm\Domain\Task\InFirm;

use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class AddTeamAsActiveProgramParticipantTest extends FirmTaskTestBase
{
    protected $task;
    protected $payload;
    protected function setUp(): void
    {
        parent::setUp();
        $this->setTeamParticipantRelatedDependency();
        $this->setTeamRelatedDependency();
        $this->setProgramRelatedDependency();
        
        $this->task = new AddTeamAsActiveProgramParticipant(
                $this->teamParticipantRepository, $this->teamRepository, $this->programRepository);
        $this->payload = new AddTeamAsActiveProgramParticipantPayload($this->teamId, $this->programId);
    }
    
    protected function execute()
    {
        $this->teamParticipantRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->teamParticipantId);
        $this->task->execute($this->firm, $this->payload);
    }
    public function test_execute_addTeamParticipantCreatedInTeamToRepository()
    {
        $this->team->expects($this->once())
                ->method('addAsActiveProgramParticipant')
                ->with($this->teamParticipantId, $this->program)
                ->willReturn($this->teamParticipant);
        $this->teamParticipantRepository->expects($this->once())
                ->method('add')
                ->with($this->teamParticipant);
        $this->execute();
    }
    public function test_execute_setPayloadsAddedTeamParticipantId()
    {
        $this->execute();
        $this->assertEquals($this->teamParticipantId, $this->payload->addedTeamParticipantId);
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
