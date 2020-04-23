<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Mission;
use Tests\TestBase;

class MissionUpdateTest extends TestBase
{

    protected $service;
    protected $missionRepository, $mission, $programCompositionId, $missionId = 'mission-id';
    protected $name = 'new mission name', $description = 'new mission description', $position = 'mission position';

    protected function setUp(): void
    {
        parent::setUp();
        $this->programCompositionId = $this->buildMockOfClass(ProgramCompositionId::class);

        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository->expects($this->any())
                ->method('ofId')
                ->with($this->programCompositionId, $this->missionId)
                ->willReturn($this->mission);

        $this->service = new MissionUpdate($this->missionRepository);
    }

    protected function execute()
    {
        $this->service->execute($this->programCompositionId, $this->missionId, $this->name, $this->description, $this->position);
    }

    public function test_execute_updateMission()
    {
        $this->mission->expects($this->once())
                ->method('update')
                ->with($this->name, $this->description, $this->position);
        $this->execute();
    }

    public function test_execute_updateRepository()
    {
        $this->missionRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

}
