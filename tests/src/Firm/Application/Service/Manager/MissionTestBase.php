<?php

namespace Tests\src\Firm\Application\Service\Manager;

use Firm\Application\Service\Manager\MissionRepository;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\MissionData;
use PHPUnit\Framework\MockObject\MockObject;

class MissionTestBase extends ManagerTestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $missionRepository;
    /**
     * 
     * @var MockObject
     */
    protected $mission;
    /**
     * 
     * @var MockObject
     */
    protected $missionData;
    protected $nextMissionId = 'nextMissionId';
    protected $missionId = 'missionId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository->expects($this->any())
                ->method('aMissionOfId')
                ->with($this->missionId)
                ->willReturn($this->mission);
        $this->missionRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextMissionId);
        $this->missionData = $this->buildMockOfClass(MissionData::class);
    }
}
