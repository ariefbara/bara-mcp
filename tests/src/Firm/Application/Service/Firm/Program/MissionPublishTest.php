<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Mission;
use Tests\TestBase;

class MissionPublishTest extends TestBase
{
    protected $service;
    protected $missionRepository, $mission, $programCompositionId, $missionId = 'mission-id';

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

        $this->service = new MissionPublish($this->missionRepository);
    }

    protected function execute()
    {
        $this->service->execute($this->programCompositionId, $this->missionId);
    }

    public function test_publish_publishMission()
    {
        $this->mission->expects($this->once())
                ->method('publish');
        $this->execute();
    }

    public function test_publish_updateRepository()
    {
        $this->missionRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
