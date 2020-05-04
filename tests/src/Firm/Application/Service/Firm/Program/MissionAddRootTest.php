<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\ProgramRepository,
    Application\Service\Firm\WorksheetFormRepository,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\WorksheetForm
};
use Tests\TestBase;

class MissionAddRootTest extends TestBase
{

    protected $service;
    protected $firmId = 'firm-id';
    
    protected $missionRepository;
    protected $programRepository, $program, $programId = 'program-id';
    protected $worksheetFormRepository, $worksheetForm, $worksheetFormId = 'worksheet-form-id';
    protected $name = 'new mission name', $description = 'new mission description', $position = 'mission position';
    protected $id = 'missionId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->missionRepository->expects($this->any())
            ->method('nextIdentity')
            ->willReturn($this->id);

        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->programId)
            ->willReturn($this->program);

        $this->worksheetFormRepository = $this->buildMockOfInterface(WorksheetFormRepository::class);
        $this->worksheetForm = $this->buildMockOfClass(WorksheetForm::class);
        $this->worksheetFormRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->worksheetFormId)
            ->willReturn($this->worksheetForm);

        $this->service = new MissionAddRoot(
            $this->missionRepository, $this->programRepository, $this->worksheetFormRepository);
    }

    protected function execute()
    {
        return $this->service->execute(
            $this->firmId, $this->programId, $this->name, $this->description, $this->worksheetFormId, $this->position);
    }
    
    public function test_execute_addMissionToRepository()
    {
        $mission = Mission::createRoot($this->program, $this->id, $this->name, $this->description, $this->worksheetForm, $this->position);
        $this->missionRepository->expects($this->once())
                ->method('add')
                ->with($mission);
        $this->execute();
    }
    public function test_execute_returnId()
    {
        $this->assertEquals($this->id, $this->execute());
    }

}
