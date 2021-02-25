<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\WorksheetForm;
use Tests\src\Firm\Application\Service\Manager\MissionTestBase;

class CreateRootMissionTest extends MissionTestBase
{
    protected $programRepository, $program, $programId = 'programId';
    protected $worksheetFormRepository, $worksheetForm, $worksheetFormId = 'worksheetFormId';
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programRepository = $this->buildMockOfClass(ProgramRepository::class);
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository->expects($this->any())
                ->method('aProgramOfId')
                ->with($this->programId)
                ->willReturn($this->program);
        
        $this->worksheetFormRepository = $this->buildMockOfClass(WorksheetFormRepository::class);
        $this->worksheetForm = $this->buildMockOfClass(WorksheetForm::class);
        $this->worksheetFormRepository->expects($this->any())
                ->method('aWorksheetFormOfId')
                ->with($this->worksheetFormId)
                ->willReturn($this->worksheetForm);
        
        $this->service = new CreateRootMission(
                $this->managerRepository, $this->programRepository, $this->worksheetFormRepository, $this->missionRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->managerId, $this->programId, $this->worksheetFormId, $this->missionData);
    }
    public function test_execute_addRootMissionCreatedByManagerToRepository()
    {
        $this->manager->expects($this->once())
                ->method('createRootMission')
                ->with($this->nextMissionId, $this->program, $this->worksheetForm, $this->missionData)
                ->willReturn($this->mission);
        $this->missionRepository->expects($this->once())
                ->method('add')
                ->with($this->mission);
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextMissionId, $this->execute());
    }
}
