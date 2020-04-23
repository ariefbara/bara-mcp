<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\WorksheetFormRepository,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\WorksheetForm
};
use Tests\TestBase;

class MissionAddBranchTest extends TestBase
{

    protected $service;
    protected $missionRepository, $mission, $programCompositionId, $missionId = 'mission-id';
    protected $worksheetFormRepository, $worksheetForm, $firmId = 'firm-id', $worksheetFormId = 'worksheet-form-id';
    protected $name = 'new mission name', $description = 'new mission description', $position = 'mission position';
    protected $id = 'newMissionId';


    protected function setUp(): void
    {
        parent::setUp();
        $this->programCompositionId = $this->buildMockOfClass(ProgramCompositionId::class);
        $this->programCompositionId->expects($this->any())
            ->method('getFirmId')
            ->willReturn($this->firmId);

        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository->expects($this->any())
            ->method('nextIdentity')
            ->willReturn($this->id);
        $this->missionRepository->expects($this->any())
            ->method('ofId')
            ->with($this->programCompositionId, $this->missionId)
            ->willReturn($this->mission);

        $this->worksheetFormRepository = $this->buildMockOfInterface(WorksheetFormRepository::class);
        $this->worksheetForm = $this->buildMockOfClass(WorksheetForm::class);
        $this->worksheetFormRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->worksheetFormId)
            ->willReturn($this->worksheetForm);

        $this->service = new MissionAddBranch($this->missionRepository, $this->worksheetFormRepository);
    }

    protected function execute()
    {
        return $this->service->execute($this->programCompositionId, $this->missionId, $this->name, $this->description,
                $this->worksheetFormId, $this->position);
    }
    
    public function test_execute_addMissionToRepository()
    {
        $this->missionRepository->expects($this->once())
            ->method('add');
        $this->execute();
    }
    public function test_execute_createBranchFromParentMissionAndAddToRepository()
    {
        $branch = $this->buildMockOfClass(Mission::class);
        $this->mission->expects($this->once())
            ->method('createBranch')
            ->with($this->anything(), $this->name, $this->description, $this->worksheetForm, $this->position)
            ->willReturn($branch);
        
        $this->missionRepository->expects($this->once())
            ->method('add')
            ->with($branch);
        
        $this->execute();
    }

}
