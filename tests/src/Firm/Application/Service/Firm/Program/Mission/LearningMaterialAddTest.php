<?php

namespace Firm\Application\Service\Firm\Program\Mission;

use Firm\ {
    Application\Service\Firm\Program\MissionRepository,
    Application\Service\Firm\Program\ProgramCompositionId,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\Program\Mission\LearningMaterial
};
use Tests\TestBase;

class LearningMaterialAddTest extends TestBase
{
    protected $learningMaterialRepository;
    protected $programCompositionId;
    protected $missionRepository, $mission, $missionId = 'missionId';
    protected $service;
    protected $id = 'nextId', $name = 'new name', $content = 'string represent content';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programCompositionId = $this->buildMockOfClass(ProgramCompositionId::class);
        
        $this->learningMaterialRepository = $this->buildMockOfInterface(LearningMaterialRepository::class);
        $this->learningMaterialRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->id);
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->missionRepository->expects($this->any())
                ->method('ofId')
                ->with($this->programCompositionId, $this->missionId)
                ->willReturn($this->mission);
        
        $this->service = new LearningMaterialAdd($this->learningMaterialRepository, $this->missionRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->programCompositionId, $this->missionId, $this->name, $this->content);
    }
    
    public function test_execute_addLearningMaterialToRepository()
    {
        $learningMaterial = new LearningMaterial($this->mission, $this->id, $this->name, $this->content);
        $this->learningMaterialRepository->expects($this->once())
                ->method('add')
                ->with($this->equalTo($learningMaterial));
        $this->execute();
    }
    public function test_execute_returnNextIdentity()
    {
        $this->assertEquals($this->id, $this->execute());
        
    }
}
